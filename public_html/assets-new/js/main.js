/* ============================================================================
   FSIA — main.js
   Homepage & global client-side effects:
     • registration-deadline countdown
     • hero sparkle generator
     • reveal-on-scroll (IntersectionObserver)
     • count-up statistics
     • sticky mobile register button + scroll-progress bar
     • FAQ accordion
     • ribbon / partners infinite marquees
     • winners carousel auto-scroll (pauses on touch & hover)
     • the multi-step "smart registration" modal (category routing,
       eligibility checks, success + WhatsApp share, payment hand-off)
   All listeners are passive where possible and respect
   prefers-reduced-motion for smooth mobile performance.
   ============================================================================ */
(function () {
  'use strict';

  /* ---- run after DOM is ready ---- */
  function ready(fn) { document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }

  ready(function () {

    /* ========================= GLOBAL EFFECTS ========================= */
    /* ====== CONFIG — change deadline here ====== */
    const REG_DEADLINE = new Date('2026-08-31T23:59:59+05:30');
    const WA_NUMBER = '919983286999';

    /* ---------- countdown ---------- */
    const cdD = document.getElementById('cd-d'), cdH = document.getElementById('cd-h'), cdM = document.getElementById('cd-m'), cdS = document.getElementById('cd-s');
    if (cdD && cdH && cdM && cdS) {
      function tick() {
        let diff = REG_DEADLINE - new Date();
        if (diff < 0) diff = 0;
        const d = Math.floor(diff / 86400000), h = Math.floor(diff / 3600000) % 24,
          m = Math.floor(diff / 60000) % 60, s = Math.floor(diff / 1000) % 60;
        cdD.textContent = String(d).padStart(2, '0'); cdH.textContent = String(h).padStart(2, '0');
        cdM.textContent = String(m).padStart(2, '0'); cdS.textContent = String(s).padStart(2, '0');
      }
      tick(); setInterval(tick, 1000);
    }

    /* ---------- hero sparkles ---------- */
    const sp = document.getElementById('sparkles');
    if (sp) {
      for (let i = 0; i < 22; i++) {
        const s = document.createElement('div'); s.className = 'sparkle';
        s.style.left = Math.random() * 100 + '%'; s.style.top = Math.random() * 85 + '%';
        s.style.animationDelay = (Math.random() * 4.5) + 's';
        s.style.animationDuration = (3.5 + Math.random() * 3) + 's';
        sp.appendChild(s);
      }
    }

    /* ---------- reveal on scroll ---------- */
    const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .12 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    /* ---------- count-up stats ---------- */
    const statIO = new IntersectionObserver(es => es.forEach(e => {
      if (!e.isIntersecting) return; statIO.unobserve(e.target);
      e.target.querySelectorAll('[data-count]').forEach(el => {
        const target = +el.dataset.count, dur = 1600, t0 = performance.now();
        (function step(t) {
          const p = Math.min((t - t0) / dur, 1);
          el.textContent = Math.floor(target * (1 - Math.pow(1 - p, 3))).toLocaleString() + (p === 1 ? '+' : '');
          if (p < 1) requestAnimationFrame(step);
        })(t0);
      });
    }), { threshold: .4 });
    document.querySelectorAll('.stats').forEach(el => statIO.observe(el));

    /* ---------- sticky register (mobile) ---------- */
    const sticky = document.getElementById('stickyReg');
    if (sticky) {
      window.addEventListener('scroll', () => { sticky.classList.toggle('show', window.scrollY > 520); }, { passive: true });
    }

    /* ---------- FAQ ---------- */
    document.querySelectorAll('.faq-q').forEach(q => q.addEventListener('click', () => q.parentElement.classList.toggle('open')));

    /* scroll progress bar */
    const prog = document.getElementById('scrollProgress');
    if (prog) {
      window.addEventListener('scroll', () => {
        const h = document.documentElement;
        prog.style.width = (h.scrollTop / (h.scrollHeight - h.clientHeight) * 100) + '%';
      }, { passive: true });
    }

    /* seamless infinite loops: duplicate ribbon + partners content */
    ['ribbonTrack', 'partnersTrack'].forEach(id => {
      const t = document.getElementById(id); if (t) t.innerHTML += t.innerHTML;
    });

    /* winners carousel auto-scroll (pauses on touch/hover) */
    const wt = document.querySelector('.win-track');
    if (wt && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
      let paused = false;
      ['mouseenter', 'touchstart'].forEach(ev => wt.addEventListener(ev, () => paused = true, { passive: true }));
      ['mouseleave', 'touchend'].forEach(ev => wt.addEventListener(ev, () => setTimeout(() => paused = false, 2500), { passive: true }));
      setInterval(() => {
        if (paused) return;
        if (wt.scrollLeft + wt.clientWidth >= wt.scrollWidth - 4) wt.scrollTo({ left: 0 });
        else wt.scrollBy({ left: 246 });
      }, 2800);
    }

    /* ========================= SMART REGISTRATION MODAL ========================= */
    /* ================= SMART FORM ================= */
    const CATS = {
      'miss-india': { name: 'Forever Miss India 2026', url: 'https://www.fsia.in/forever-miss-india-new.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 35], marital: true },
      'mrs-india': { name: 'Forever Mrs India 2026', url: 'https://www.fsia.in/forever-mrs-india-new.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 60] },
      'miss-teen': { name: 'Forever Miss Teen India 2026', url: 'https://www.fsia.in/forever-miss-teen-india-new.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [13, 19] },
      'kids': { name: 'Forever Star Kids Contest 2026', url: 'https://www.fsia.in/register-star-kids.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [4, 12], parent: true },
      'miss-fsia': { name: 'Miss FSIA International 2026', url: 'https://www.fsia.in/register-miss-fsia-intl.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 35], marital: true },
      'mrs-fsia': { name: 'Mrs FSIA International 2026', url: 'https://www.fsia.in/register-mrs-fsia-intl.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 60] },
      'miss-world': { name: 'Miss World 2026', url: 'https://www.fsia.in/miss-world-beauty-pageant.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 35], marital: true },
      'mrs-world': { name: 'Mrs World 2026', url: 'https://www.fsia.in/mrs-world-beauty-pageant.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 60] },
      'miss-universe': { name: 'Miss Universe 2026', url: 'https://www.fsia.in/miss-universe-beauty-pageant.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 35], marital: true },
      'mrs-universe': { name: 'Mrs Universe 2026', url: 'https://www.fsia.in/mrs-universe-beauty-pageant.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', age: [18, 60] },
      'intl-award': { name: 'International Award 2026', url: 'https://www.fsia.in/international-award.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'super-woman': { name: 'Super Woman Award 2026', url: 'https://www.fsia.in/super-woman-award.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'super-hero': { name: 'Super Hero Award 2026', url: 'https://www.fsia.in/super-hero-award.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'business': { name: 'Business Awards 2026', url: 'https://www.fsia.in/business-awards.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'bharat-national': { name: 'Bharat National Award 2026', url: 'https://www.fsia.in/bharat-national-awards.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'forever-achievers': { name: 'Forever Achievers 2026', url: 'https://www.fsia.in/forever-achievers.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'bharat-couture': { name: 'Bharat Couture Week 2026', url: 'https://www.fsia.in/register-bharat-couture.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true, portfolio: true },
      'intl-achievers': { name: 'International Achievers Award', url: 'https://www.fsia.in/infinity-achievers.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'designer': { name: 'Forever Fashion Designers 2026', url: 'https://www.fsia.in/register-designer.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true, portfolio: true },
      'makeup': { name: 'Forever Makeup Artist 2026', url: 'https://www.fsia.in/register-makeup.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true, portfolio: true },
      'partner': { name: 'Channel Partner 2026', url: 'https://www.fsia.in/register-partner.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true },
      'nominate': { name: 'Nominate Yourself for Award 2026', url: 'https://www.fsia.in/register-nominate.php?_gl=1*cx4jbt*_ga*MTMzMzE1MzkzNS4xNzgxNjk5OTg2*_ga_Y4WSHCXRGB*czE3ODE3NjM4NzkkbzQkZzAkdDE3ODE3NjM4NzkkajYwJGwwJGg1MDg5MjgxNzk.', profession: true }
    };
    let data = { category: null }, currentStep = 1;
    const modal = document.getElementById('modal');

    function openForm(cat) {
      if (!modal) return;
      modal.classList.add('open'); document.body.style.overflow = 'hidden';
      if (cat && CATS[cat]) { selectCategory(cat, false); goStep(2); }
      else goStep(1);
    }
    function closeForm() { 
      if (!modal) return;
      modal.classList.remove('open'); document.body.style.overflow = ''; 
    }
    if (modal) {
      modal.addEventListener('click', e => { if (e.target === modal) closeForm(); });
    }

    function goStep(n) {
      currentStep = n;
      document.querySelectorAll('.fstep').forEach(s => s.classList.toggle('active', +s.dataset.step === n));
      const progressFill = document.getElementById('progressFill');
      if (progressFill) progressFill.style.width = (n / 5 * 100) + '%';
      const stepLabel = document.getElementById('stepLabel');
      if (stepLabel) stepLabel.textContent = 'Step ' + n + ' of 5';
      const progressWrap = document.getElementById('progressWrap');
      if (progressWrap) progressWrap.style.display = n === 5 ? 'none' : '';
      const formCard = document.getElementById('formCard');
      if (formCard) formCard.scrollTop = 0;
    }

    /* step 1: category cards */
    const catGrid = document.getElementById('catGrid');
    if (catGrid) {
      catGrid.addEventListener('click', e => {
        const c = e.target.closest('.choice'); if (!c) return;
        selectCategory(c.dataset.cat, true);
      });
    }
    function selectCategory(cat, auto) {
      data.category = cat;
      document.querySelectorAll('#catGrid .choice').forEach(c => c.classList.toggle('selected', c.dataset.cat === cat));
      configStep2(cat);
      if (auto) setTimeout(() => goStep(2), 400);
    }
    function configStep2(cat) {
      const cfg = CATS[cat];
      const eligHint = document.getElementById('eligHint');
      if (eligHint) eligHint.textContent = 'Registering for ' + cfg.name;
      const fAge = document.getElementById('fAge');
      if (fAge) fAge.style.display = cfg.age ? '' : 'none';
      const ageLabel = document.getElementById('ageLabel');
      if (ageLabel) ageLabel.textContent = cat === 'kids' ? "Child's age" : 'Your age';
      const fMarital = document.getElementById('fMarital');
      if (fMarital) fMarital.style.display = cfg.marital ? '' : 'none';
      const fProfession = document.getElementById('fProfession');
      if (fProfession) fProfession.style.display = cfg.profession ? '' : 'none';
      const fParent = document.getElementById('fParent');
      if (fParent) fParent.style.display = cfg.parent ? '' : 'none';
      const fPortfolio = document.getElementById('fPortfolio');
      if (fPortfolio) fPortfolio.style.display = cfg.portfolio ? '' : 'none';
      const suggestBox = document.getElementById('suggestBox');
      if (suggestBox) suggestBox.classList.remove('show');
    }
    /* friendly redirect logic */
    function suggest(catKey, why) {
      const box = document.getElementById('suggestBox');
      if (!box) return;
      const suggestName = document.getElementById('suggestName');
      if (suggestName) suggestName.textContent = CATS[catKey].name;
      const suggestWhy = document.getElementById('suggestWhy');
      if (suggestWhy) suggestWhy.textContent = why;
      const suggestBtn = document.getElementById('suggestBtn');
      if (suggestBtn) suggestBtn.onclick = () => { selectCategory(catKey, false); box.classList.remove('show'); };
      box.classList.add('show');
    }
    function validateStep2() {
      const cfg = CATS[data.category]; let ok = true;
      const ageF = document.getElementById('fAge');
      const ageEl = document.getElementById('age');
      const age = ageEl ? +ageEl.value : 0;
      if (ageF) ageF.classList.remove('invalid');
      if (cfg.age) {
        if (!age) { 
          if (ageF) ageF.classList.add('invalid'); 
          const ageErr = document.getElementById('ageErr');
          if (ageErr) ageErr.textContent = 'Please enter a valid age.'; 
          ok = false; 
        }
        else if (age < cfg.age[0] || age > cfg.age[1]) {
          if (ageF) ageF.classList.add('invalid');
          const ageErr = document.getElementById('ageErr');
          if (ageErr) ageErr.textContent = 'For ' + cfg.name + ' the age range is ' + cfg.age[0] + '–' + cfg.age[1] + '.';
          if (data.category === 'miss-teen' && age > 19) suggest('miss-india', 'Based on your age, Miss India may be the right stage for you.');
          if (data.category === 'miss-teen' && age < 13) suggest('kids', 'Based on the age entered, the Star Kids Contest may be the right fit.');
          ok = false;
        }
      }
      const maritalEl = document.getElementById('marital');
      if (cfg.marital && maritalEl && maritalEl.value === 'married') {
        suggest('mrs-india', 'Forever Mrs India celebrates married women on the national stage — switch in one tap.');
        ok = false;
      }
      const cityEl = document.getElementById('city');
      const cityF = document.getElementById('fCity') || (cityEl ? cityEl.parentElement : null);
      if (cityF) cityF.classList.remove('invalid');
      if (cityEl && !cityEl.value.trim()) { 
        if (cityF) cityF.classList.add('invalid'); 
        ok = false; 
      }
      if (ok) {
        const stateEl = document.getElementById('state');
        const professionEl = document.getElementById('profession');
        const parentEl = document.getElementById('parent');
        data.age = age || ''; 
        data.marital = maritalEl ? maritalEl.value : '';
        data.profession = professionEl ? professionEl.value : ''; 
        data.parent = parentEl ? parentEl.value : '';
        data.state = stateEl ? stateEl.value : ''; 
        data.city = cityEl ? cityEl.value : '';
        goStep(3);
      }
    }
    function validateStep3() {
      let ok = true;
      const nF = document.getElementById('fName'), pF = document.getElementById('fPhone');
      if (nF) nF.classList.remove('invalid'); 
      if (pF) pF.classList.remove('invalid');
      const nameEl = document.getElementById('name');
      const phoneEl = document.getElementById('phone');
      const name = nameEl ? nameEl.value.trim() : '';
      const phone = phoneEl ? phoneEl.value.replace(/\D/g, '') : '';
      if (name.length < 2) { if (nF) nF.classList.add('invalid'); ok = false; }
      if (!/^[6-9]\d{9}$/.test(phone)) { if (pF) pF.classList.add('invalid'); ok = false; }
      if (ok) {
        const emailEl = document.getElementById('email');
        const instaEl = document.getElementById('insta');
        data.name = name; data.phone = phone;
        data.email = emailEl ? emailEl.value : ''; 
        data.insta = instaEl ? instaEl.value : '';
        /* DEVELOPER NOTE: partial lead capture — POST {category, name, phone, status:'incomplete'} to your backend here */
        goStep(4);
      }
    }
    function finishForm() {
      const aboutEl = document.getElementById('about');
      data.about = aboutEl ? aboutEl.value : '';
      const cfg = CATS[data.category];

      // Populate success screen
      const succCat = document.getElementById('succCat');
      if (succCat) succCat.textContent = cfg.name;
      const succCat2 = document.getElementById('succCat2');
      if (succCat2) succCat2.textContent = cfg.name;
      const succName = document.getElementById('succName');
      if (succName) succName.textContent = data.name;
      const succCity = document.getElementById('succCity');
      if (succCity) succCity.textContent = data.city;
      const succPhone = document.getElementById('succPhone');
      if (succPhone) succPhone.textContent = data.phone;

      // DEVELOPER NOTE: POST full `data` object (status:'complete') to your backend here, e.g. api/register.php
      // For now, save to sessionStorage so payment page can access it
      sessionStorage.setItem('fsiaReg', JSON.stringify({
        category: cfg.name,
        name: data.name,
        phone: data.phone,
        email: data.email,
        city: data.city,
        state: data.state,
        insta: data.insta,
        timestamp: new Date().toISOString()
      }));

      goStep(5);
      confetti();
    }

    function goToPayment() {
      const cfg = CATS[data.category];
      if (cfg && cfg.url) {
        window.open(cfg.url, '_blank');
      } else {
        window.location.href = 'payment.php';
      }
    }

    function shareWhatsApp() {
      const cfg = CATS[data.category];
      const msg = 'Hi! I just registered for ' + cfg.name + ' with Forever Star India! Excited to begin this journey!';
      window.open('https://wa.me/?text=' + encodeURIComponent(msg), '_blank');
    }
    function confetti() {
      if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      const colors = ['#D4AF37', '#A6093D', '#F2C4CF', '#B8962E', '#fff'];
      const card = document.getElementById('formCard');
      if (!card) return;
      for (let i = 0; i < 46; i++) {
        const c = document.createElement('div'); c.className = 'confetti';
        c.style.left = Math.random() * 100 + '%';
        c.style.background = colors[Math.floor(Math.random() * colors.length)];
        c.style.animationDuration = (1.8 + Math.random() * 1.6) + 's';
        c.style.animationDelay = (Math.random() * .5) + 's';
        card.appendChild(c); setTimeout(() => c.remove(), 4200);
      }
    }
    /* close on Escape */
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeForm(); });

    /* auto-open form when arriving with ?register=1 or ?category=slug */
    const params = new URLSearchParams(location.search);
    if (params.get('register') === '1') openForm();
    else if (params.get('category') && CATS[params.get('category')]) openForm(params.get('category'));

    /* Dropdown menu toggle (simplified - CSS handles most of it now) */
    function openDropdown() { } /* CSS hover handles this */
    function closeDropdown() { } /* CSS hover handles this */

    /* Open form with specific category */
    function openFormWithCat(e, cat) {
      e.preventDefault();
      openForm(cat);
    }

    // Expose functions globally for inline HTML event handlers
    window.openForm = openForm;
    window.closeForm = closeForm;
    window.goStep = goStep;
    window.validateStep2 = validateStep2;
    window.validateStep3 = validateStep3;
    window.finishForm = finishForm;
    window.goToPayment = goToPayment;
    window.shareWhatsApp = shareWhatsApp;
    window.openFormWithCat = openFormWithCat;

  }); /* end ready */
})();
