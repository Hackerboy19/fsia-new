# FSIA Admin Panel — Setup Guide

You now have a real content management system: a login-protected admin where you
create blog posts, edit page text, upload images, and change site settings — and
everything saves to a live database, so changes appear for visitors **immediately**
(no re-uploading files).

It runs on **Supabase** (free): one service that gives you the database, image
storage, and login. Setup is a one-time job of about 15–20 minutes.

---

## What you got

```
admin/
  ├─ admin.html            ← the admin panel (your /admin login)
  ├─ fsia-config.js        ← paste your 2 Supabase keys here (one time)
  ├─ fsia-cms.js           ← makes text on existing pages editable
  └─ supabase-setup.sql    ← run once to build the database
blog.html                  ← public blog listing (reads live posts)
blog-post.html             ← public single-post page
```

---

## Step 1 — Create a free Supabase project

1. Go to **https://supabase.com** → sign up (free) → **New project**.
2. Give it a name (e.g. `fsia`), set a database password (save it somewhere), pick a region close to India (e.g. Mumbai / Singapore).
3. Wait ~2 minutes for it to finish setting up.

## Step 2 — Build the database

1. In your Supabase project, open **SQL Editor** (left menu) → **New query**.
2. Open the file **admin/supabase-setup.sql**, copy ALL of it, paste it in, click **Run**.
3. You should see "Success". This creates your tables, security rules, the image
   storage bucket, and some starter content.

## Step 3 — Get your 2 keys

1. In Supabase, go to **Project Settings** (gear icon) → **API**.
2. Copy the **Project URL** (looks like `https://abcd1234.supabase.co`).
3. Copy the **anon public** key (a long string). *(The "anon public" key is safe to
   put in your website — your data is protected by the security rules.)*
4. Open **admin/fsia-config.js** and paste both values between the quotes:

```js
window.FSIA_CONFIG = {
  SUPABASE_URL: "https://abcd1234.supabase.co",
  SUPABASE_ANON_KEY: "eyJhbGci....your-long-key...."
};
```

## Step 4 — Create your admin login

1. In Supabase, go to **Authentication** → **Users** → **Add user** → **Create new user**.
2. Enter the email + password you want to log in with. Tick "Auto Confirm User".
3. That email/password is now your admin login.

## Step 5 — Upload to your site & deploy

1. Upload these to your `fsia-new` GitHub repo (keep the `admin/` folder structure):
   - the whole `admin/` folder
   - `blog.html`
   - `blog-post.html`
2. Commit. Vercel redeploys in a few minutes.

## Step 6 — Log in and use it

- Go to **https://your-site.com/admin/admin.html**
- Sign in with the email/password from Step 4.
- You'll see four tabs:
  - **Blog / News** — write, publish, edit, delete posts. Published posts appear instantly on `blog.html`.
  - **Page Text** — edit the editable text blocks across your site.
  - **Images** — upload photos, copy their URL to use anywhere, manage your library.
  - **Settings** — phone numbers, email, WhatsApp.

---

## Connecting your blog to the site

Add a "Blog" link in your site navigation pointing to `blog.html`. That's it — the
blog page lists everything you publish, and each post opens at
`blog-post.html?slug=...` automatically.

---

## Making text on EXISTING pages editable (optional)

Your current pages (home, about, etc.) have hard-coded text. To let the admin edit
a specific piece of text on a page:

1. Add this once, just before `</body>` on that page:

```html
<script src="admin/fsia-config.js"></script>
<script type="module" src="admin/fsia-cms.js"></script>
```
   *(adjust the path — e.g. `admin/...` from the homepage)*

2. Mark the element you want editable with `data-cms="PAGE:BLOCK_KEY"`:

```html
<h1 data-cms="home:hero_title">India's Biggest Beauty Pageant & Awards Platform</h1>
```

3. Make sure a matching block exists in the **Page Text** tab (the starter SQL already
   added `home:hero_title`, `home:hero_subtitle`, `home:announcement`, and a couple more —
   you can add more rows in Supabase → Table Editor → `page_blocks`).

Now editing that block in the admin updates the live page. If the database is ever
unreachable, the original hard-coded text shows — so the page never breaks.

> Want me to wire your homepage hero + announcement bar to the CMS for you as a
> working example? Just ask and I'll add the `data-cms` tags and the script include.

---

## Security notes (plain English)

- The "anon public" key in `fsia-config.js` is **meant** to be public. Writing
  (creating/editing/deleting) requires being logged in — enforced by the database
  rules from the SQL file, which the public key cannot bypass.
- Only people you create in Supabase → Authentication can log into the admin.
- To add another admin later, just add another user there.

## Costs

Supabase's free tier covers a small site like this comfortably (database + 1GB of
image storage + 50,000 monthly active users). You only pay if you grow well beyond that.

---

## Troubleshooting

- **"Not connected yet" on the login screen** → you haven't pasted your keys into
  `admin/fsia-config.js`, or the file didn't deploy.
- **Login fails** → confirm the user exists in Supabase → Authentication → Users, and
  that you ticked "Auto Confirm User".
- **Images won't upload** → make sure the SQL ran fully (it creates the `media` storage
  bucket and its permissions).
- **Blog shows "No posts"** → you have drafts but nothing **Published** yet. Open a post
  and click **Publish**.
