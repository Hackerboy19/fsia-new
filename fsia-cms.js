/* ============================================================
   FSIA CMS — page content loader
   Put this on any existing page to make text editable from the admin.

   1) Include (just before </body>):
        <script src="admin/fsia-config.js"></script>
        <script type="module" src="admin/fsia-cms.js"></script>

   2) Mark any element you want editable with data-cms="PAGE:BLOCK_KEY"
        <h1 data-cms="home:hero_title">India's Biggest Pageant...</h1>
        <p  data-cms="home:hero_subtitle">From city auditions...</p>

   The text you type in the admin (Page Text tab) will replace it live.
   The block must exist in the page_blocks table (admin shows them).
   If Supabase isn't reachable, the original hard-coded text stays — safe.
   ============================================================ */
(async function(){
  try{
    const cfg = window.FSIA_CONFIG;
    if(!cfg || !cfg.SUPABASE_URL || cfg.SUPABASE_URL.includes("PASTE")) return;
    const els = document.querySelectorAll("[data-cms]");
    if(!els.length) return;

    const { createClient } = await import("https://esm.sh/@supabase/supabase-js@2");
    const sb = createClient(cfg.SUPABASE_URL, cfg.SUPABASE_ANON_KEY);
    const { data, error } = await sb.from("page_blocks").select("page,block_key,value");
    if(error || !data) return;

    const map = {};
    data.forEach(b => { map[b.page+":"+b.block_key] = b.value; });

    els.forEach(el => {
      const key = el.getAttribute("data-cms");
      if(map[key] != null && map[key] !== "") el.innerHTML = map[key];
    });
  }catch(e){ /* fail silent — keep original page text */ }
})();
