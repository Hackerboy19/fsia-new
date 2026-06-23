<?php
// FSIA live-site header configuration init
?>
<!-- ===== FSIA live-site header v3 ===== -->
<style>
  /* 🛡️ Strict CSS Isolation overrides to prevent Tailwind from breaking the design */
  .fsia-master-header-isolation {
    all: unset;
    display: block !important;
    width: 100% !important;
    box-sizing: border-box !important;
    background: #fff !important;
    font-family: Arial, Helvetica, sans-serif !important;
  }
  .fsia-master-header-isolation * {
    box-sizing: border-box !important;
  }
  
  /* 1. Announcement Bar Settings */
  .fsia-master-header-isolation .fsia-announce {
    display: block !important;
    background: #1e293b !important;
    color: #cbd5e1 !important;
    text-align: center !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
  }
  .fsia-master-header-isolation .fsia-announce a {
    color: #f59e0b !important;
    text-decoration: none !important;
    font-weight: bold !important;
    display: inline-block !important;
  }

  /* 2. Top Utility Bar Settings */
  .fsia-master-header-isolation .fsia-topbar {
    display: block !important;
    background: #f1f5f9 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 10px 0 !important;
  }
  .fsia-master-header-isolation .tb-wrap {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 0 15px !important;
  }
  .fsia-master-header-isolation .tb-contact {
    display: flex !important;
    gap: 20px !important;
  }
  .fsia-master-header-isolation .tb-contact a {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    color: #334155 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
  }
  .fsia-master-header-isolation .ic {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 22px !important;
    height: 22px !important;
    background: #e2e8f0 !important;
    border-radius: 50% !important;
    color: #475569 !important;
  }
  .fsia-master-header-isolation .fsia-logo {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    text-align: center !important;
    text-decoration: none !important;
    color: #1e293b !important;
  }
  .fsia-master-header-isolation .fsia-logo strong {
    font-size: 15px !important;
    letter-spacing: 1px !important;
    color: #7f1d1d !important;
  }
  .fsia-master-header-isolation .fsia-logo span {
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 2px !important;
    color: #64748b !important;
  }
  .fsia-master-header-isolation .tb-socials {
    display: flex !important;
    gap: 8px !important;
  }
  .fsia-master-header-isolation .tb-socials a {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 26px !important;
    height: 26px !important;
    background: #e2e8f0 !important;
    border-radius: 50% !important;
    color: #475569 !important;
    text-decoration: none !important;
  }

  /* 3. Navigation Bar Settings */
  .fsia-master-header-isolation .fsia-nav-wrap {
    display: block !important;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }
  .fsia-master-header-isolation .fsia-nav {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 8px 15px !important;
  }
  .fsia-master-header-isolation .fsia-nav-links {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
  }
  .fsia-master-header-isolation .fsia-nav-links a,
  .fsia-master-header-isolation .fsia-reg-btn {
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: bold !important;
    text-decoration: none !important;
    padding: 8px 4px !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
  }
  .fsia-master-header-isolation .fsia-cta-btn {
    background: #b91c1c !important;
    color: #ffffff !important;
    padding: 8px 20px !important;
    border-radius: 20px !important;
    font-size: 13px !important;
    font-weight: bold !important;
    text-decoration: none !important;
    display: inline-block !important;
  }

  /* 4. Sub Navigation Strip Bar Settings */
  .fsia-master-header-isolation .fsia-strip {
    display: block !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 10px 0 !important;
  }
  .fsia-master-header-isolation .st-wrap {
    display: flex !important;
    align-items: center !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 0 15px !important;
    gap: 20px !important;
  }
  .fsia-master-header-isolation .fsia-search {
    display: inline-flex !important;
    align-items: center !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 15px !important;
    padding: 4px 12px !important;
    width: 180px !important;
  }
  .fsia-master-header-isolation .fsia-search input {
    border: none !important;
    outline: none !important;
    font-size: 12px !important;
    width: 100% !important;
    padding-left: 5px !important;
    background: transparent !important;
  }
  .fsia-master-header-isolation .fsia-quicknav {
    flex-grow: 1 !important;
    overflow-x: auto !important;
  }
  .fsia-master-header-isolation .fsia-quicknav-inner {
    display: flex !important;
    gap: 8px !important;
    white-space: nowrap !important;
  }
  .fsia-master-header-isolation .fsia-quicknav-inner a {
    display: inline-block !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    padding: 4px 14px !important;
    border-radius: 15px !important;
    font-size: 11px !important;
    color: #475569 !important;
    text-decoration: none !important;
    font-weight: 500 !important;
  }
  .fsia-master-header-isolation .fsia-quicknav-inner a.active {
    background: #7f1d1d !important;
    color: #ffffff !important;
    border-color: #7f1d1d !important;
  }

  /* Structural Dropdowns and Mobile Toggles */
  .fsia-master-header-isolation .fsia-reg {
    position: relative !important;
    display: inline-block !important;
  }
  .fsia-master-header-isolation .fsia-reg-menu {
    display: none;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    border-radius: 8px !important;
    width: 240px !important;
    z-index: 9999 !important;
  }
  .fsia-master-header-isolation .fsia-reg:hover .fsia-reg-menu,
  .fsia-master-header-isolation .fsia-reg:focus-within .fsia-reg-menu {
    display: block !important;
  }
  .fsia-master-header-isolation .fsia-reg-menu a {
    display: block !important;
    padding: 8px 16px !important;
    font-size: 12px !important;
    color: #334155 !important;
    text-decoration: none !important;
  }
  .fsia-master-header-isolation .fsia-reg-menu a:hover {
    background: #f1f5f9 !important;
  }
  .fsia-master-header-isolation .rm-head {
    background: #f1f5f9 !important;
    padding: 8px 16px !important;
    font-size: 11px !important;
    font-weight: bold !important;
    color: #64748b !important;
  }
  .fsia-master-header-isolation .fsia-hamburger,
  .fsia-master-header-isolation .fsia-nav-logo,
  .fsia-master-header-isolation .fsia-mobile-cta-btn,
  .fsia-master-header-isolation .fsia-nav-socials {
    display: none !important;
  }
</style>

<div class="fsia-master-header-isolation">
  <!-- GTM noscript -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T3674CM" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

  <!-- 1. ANNOUNCEMENT BAR -->
  <div class="fsia-announce">
    Applications are Invited for <strong>Miss India 2026</strong>, <strong>Mrs India 2026</strong>, and <strong>Miss Teen India 2026</strong> from PAN India — Limited Entries!
    &nbsp;<a href="/forever-miss-india-new.php">Register Now →</a>
  </div>

  <!-- 2. TOP UTILITY BAR -->
  <div class="fsia-topbar">
    <div class="tb-wrap">
      <div class="tb-contact">
        <a href="tel:+919983286999">
          <span class="ic ic-phone">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 3.09 4.18 2 2 0 0 1 5.07 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </span>
          +91-99832-86999
        </a>
        <a href="mailto:starindiaaward@gmail.com">
          <span class="ic ic-mail">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
          </span>
          care@fsia.in
        </a>
      </div>
      <a href="/index.php" class="fsia-logo">
        <strong>FOREVER STAR INDIA</strong>
        <span>Beauty Pageants &amp; Awards</span>
      </a>
      <div class="tb-socials">
        <a href="https://www.facebook.com/Foreverstarindiaawards/" target="_blank" rel="noopener">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        <a href="https://www.youtube.com/c/foreverstarindiaaward" target="_blank" rel="noopener">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 0 0 1.96-1.97A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
        </a>
        <a href="https://www.instagram.com/fsia_forever/" target="_blank" rel="noopener">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <!-- 3. MAIN NAV -->
  <nav class="fsia-nav-wrap">
    <div class="fsia-nav">
      <div class="fsia-nav-links">
        <a href="/index.php">Home</a>
        <a href="/about.php">About</a>
        <div class="fsia-reg">
          <button class="fsia-reg-btn">
            Registration ▾
          </button>
          <div class="fsia-reg-menu">
            <div class="rm-head">All Events &amp; Awards 2026</div>
            <a href="/super-hero-award.php">Super Hero Award 2026</a>
            <a href="/super-woman-award.php">Super Woman Award 2026</a>
            <a href="/business-awards.php">Business Awards 2026</a>
            <a href="/forever-miss-india-new.php">Forever Miss India 2026</a>
            <a href="/forever-mrs-india-new.php">Forever Mrs India 2026</a>
          </div>
        </div>
        <a href="/faq.php">FAQ</a>
        <a href="/contact.php">Contact</a>
        <a href="/our-team.php">Our Team</a>
        <a href="/news.php">News Coverages</a>
        <a href="/winners.php">Contestant</a>
        <a href="https://www.fsia.in/online-franchise-application" target="_blank">Franchise</a>
      </div>
      <a href="/forever-miss-india-new.php" class="fsia-cta-btn">Apply Now</a>
    </div>
  </nav>

  <!-- 4. QUICK-NAV STRIP -->
  <div class="fsia-strip">
    <div class="st-wrap">
      <div class="fsia-search">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" placeholder="F-sia Search…">
      </div>
      <nav class="fsia-quicknav">
        <div class="fsia-quicknav-inner">
          <a href="#" class="active">Achievers</a>
          <a href="#">Contestant</a>
          <a href="#">Awardee</a>
          <a href="#">Gallery</a>
          <a href="#">National Award</a>
          <a href="#">Fashion Gallery</a>
          <a href="#">Winners</a>
          <a href="#">Finalist</a>
          <a href="#">Achievers Gallery</a>
        </div>
      </nav>
    </div>
  </div>
</div>

<?php
/**
 * ------------------------------------------------------------------------
 * PREMIUM COMPONENT HOOK ENHANCEMENTS: Dynamic Page Configurations
 * ------------------------------------------------------------------------
 */
if (!function_exists('render_form_hero')) {
    function render_form_hero() {
        $current_page = basename($_SERVER['SCRIPT_NAME']);
        $title = "Miss Universe 2026";
        
        if (strpos($current_page, 'mrs-universe') !== false) {
            $title = "Mrs Universe 2026";
        } elseif (strpos($current_page, 'miss-world') !== false) {
            $title = "Miss World 2026";
        } elseif (strpos($current_page, 'mrs-world') !== false) {
            $title = "Mrs World 2026";
        }
        ?>
        <div class="text-center max-w-3xl mx-auto mb-12 pt-4 px-4 font-sans">
            <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 rounded-full px-4 py-1.5 mb-4" style="display:inline-flex !important;">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-[0.25em]">
                    Official International Selection Framework
                </span>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-2 font-serif" style="font-family:'Playfair Display', serif !important;">
                <?= htmlspecialchars($title) ?>
            </h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.4em] block mb-6">BY FOREVER STAR INDIA</p>
            
            <div class="w-20 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent mx-auto rounded-full mb-8"></div>
            
            <div class="bg-gradient-to-r from-slate-900 to-slate-950 text-white rounded-2xl p-6 md:p-8 shadow-xl text-left border border-slate-800 relative overflow-hidden mb-8" style="display:block !important; text-align:left !important;">
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center" style="display:grid !important;">
                    <div class="md:col-span-8 space-y-3">
                        <h3 class="text-lg font-bold text-amber-400 font-serif" style="font-family:'Playfair Display', serif !important; color:#fbbf24 !important;">Secure Your Place on the Global Stage</h3>
                        <p class="text-slate-300 text-xs md:text-sm leading-relaxed font-light" style="color:#cbd5e1 !important;">
                            As the globe's premier platform for world-class talent and pageantry, <span class="font-semibold text-white">Forever Star India</span> proudly hosts an elite international competition across <span class="text-amber-400 font-medium">139 nations</span>. Completing your secure participation profile below grants you immediate entry into our verified international selection ecosystem from anywhere in the world.
                        </p>
                    </div>
                    <div class="md:col-span-4 flex flex-col justify-center items-stretch md:border-l border-slate-800 md:pl-6" style="display:flex !important;">
                        <div class="text-center p-2.5 bg-white/5 rounded-xl border border-white/10">
                            <span class="text-xl block mb-0.5">🌎</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Status Zone</span>
                            <span class="text-xs font-semibold text-emerald-400" style="color:#34d399 !important;">Applications Open Globally</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>