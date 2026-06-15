-- ============================================================
--  FOREVER STAR INDIA — CMS DATABASE SETUP
--  Run ONCE in Supabase:  Dashboard → SQL Editor → New query
--  Paste everything below → press RUN.
-- ============================================================

create extension if not exists "pgcrypto";

-- ---------- 1) BLOG / NEWS POSTS ----------
create table if not exists public.posts (
  id           uuid primary key default gen_random_uuid(),
  slug         text unique not null,
  title        text not null,
  excerpt      text,
  cover_url    text,
  author       text default 'Forever Star India',
  category     text default 'News',
  body         text,
  status       text default 'draft' check (status in ('draft','published')),
  published_at timestamptz,
  created_at   timestamptz default now(),
  updated_at   timestamptz default now()
);

-- ---------- 2) EDITABLE PAGE TEXT BLOCKS ----------
create table if not exists public.page_blocks (
  id         uuid primary key default gen_random_uuid(),
  page       text not null,
  block_key  text not null,
  label      text,
  value      text,
  updated_at timestamptz default now(),
  unique (page, block_key)
);

-- ---------- 3) MEDIA LIBRARY (winners, gallery, etc.) ----------
create table if not exists public.media (
  id         uuid primary key default gen_random_uuid(),
  url        text not null,
  title      text,
  alt        text,
  category   text default 'gallery',
  created_at timestamptz default now()
);

-- ---------- 4) SITE SETTINGS (phones, email, announcement) ----------
create table if not exists public.settings (
  key        text primary key,
  value      text,
  updated_at timestamptz default now()
);

-- ============================================================
--  ROW-LEVEL SECURITY
--  Visitors can READ published content.
--  Only a logged-in admin can WRITE.
-- ============================================================
alter table public.posts        enable row level security;
alter table public.page_blocks  enable row level security;
alter table public.media        enable row level security;
alter table public.settings     enable row level security;

-- POSTS: public reads published; admin reads/writes everything
drop policy if exists "posts public read"  on public.posts;
drop policy if exists "posts admin read"   on public.posts;
drop policy if exists "posts admin write"  on public.posts;
create policy "posts public read" on public.posts
  for select using (status = 'published');
create policy "posts admin read" on public.posts
  for select to authenticated using (true);
create policy "posts admin write" on public.posts
  for all to authenticated using (true) with check (true);

-- PAGE BLOCKS: public read, admin write
drop policy if exists "blocks public read" on public.page_blocks;
drop policy if exists "blocks admin write" on public.page_blocks;
create policy "blocks public read" on public.page_blocks
  for select using (true);
create policy "blocks admin write" on public.page_blocks
  for all to authenticated using (true) with check (true);

-- MEDIA: public read, admin write
drop policy if exists "media public read" on public.media;
drop policy if exists "media admin write" on public.media;
create policy "media public read" on public.media
  for select using (true);
create policy "media admin write" on public.media
  for all to authenticated using (true) with check (true);

-- SETTINGS: public read, admin write
drop policy if exists "settings public read" on public.settings;
drop policy if exists "settings admin write" on public.settings;
create policy "settings public read" on public.settings
  for select using (true);
create policy "settings admin write" on public.settings
  for all to authenticated using (true) with check (true);

-- ============================================================
--  STORAGE BUCKET FOR IMAGES
-- ============================================================
insert into storage.buckets (id, name, public)
values ('media', 'media', true)
on conflict (id) do nothing;

drop policy if exists "media bucket public read" on storage.objects;
drop policy if exists "media bucket admin write" on storage.objects;
create policy "media bucket public read" on storage.objects
  for select using (bucket_id = 'media');
create policy "media bucket admin write" on storage.objects
  for all to authenticated using (bucket_id = 'media') with check (bucket_id = 'media');

-- ============================================================
--  STARTER CONTENT (so the admin has something to edit)
-- ============================================================
insert into public.page_blocks (page, block_key, label, value) values
  ('home','hero_title','Homepage — Hero heading','India''s Biggest Beauty Pageant & Awards Platform'),
  ('home','hero_subtitle','Homepage — Hero subtext','From city auditions to the national crown — your journey to the spotlight starts here.'),
  ('home','announcement','Announcement bar text','Registrations open for Miss India, Mrs India & Miss Teen India 2026 — Limited Entries!'),
  ('about','intro','About page — intro paragraph','Forever Star India is India''s most unique platform for beauty pageants and award shows.'),
  ('contact','address','Contact page — address',' ')
on conflict (page, block_key) do nothing;

insert into public.settings (key, value) values
  ('phone_primary','+91-99832-86999'),
  ('phone_secondary','+91-99835-99666'),
  ('email','starindiaaward@gmail.com'),
  ('whatsapp','919983286999')
on conflict (key) do nothing;

-- ============================================================
--  DONE.  Next: create your admin login user in
--  Dashboard → Authentication → Users → "Add user".
-- ============================================================
