/* ============================================================================
   FSIA — forms-handler.js
   Registration-page logic (every register-*.php form):
     • browser-level input constraints (type/pattern/required/inputmode)
     • required-field validation: Name, Email, Phone (Indian mobile)
     • 48-hour payment-urgency countdown loop
     • secure local routing of a successful registration to the
       confirmation dashboard (registration-success.php)
   Vanilla JS, mobile-touch optimised, no HTML, no dependencies.
   ============================================================================ */
(function () {
  'use strict';

  function ready(fn) { document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }

  ready(function () {

    var form = document.getElementById('registrationForm');
    if (!form) return;

    /* The page declares its category name on the <form data-category="..."> attribute.
       Falls back to the document title if absent. */
    var CATEGORY = form.getAttribute('data-category') ||
      (document.title.split('|')[0] || 'Forever Star India').trim();

    /* ---------------------------------------------------------------
       1. BROWSER-LEVEL INPUT CONSTRAINTS (progressive enhancement)
       --------------------------------------------------------------- */
    function constrain(name, attrs) {
      var el = form.querySelector('[name="' + name + '"]');
      if (!el) return;
      Object.keys(attrs).forEach(function (k) { el.setAttribute(k, attrs[k]); });
    }
    constrain('firstName', { required: '', autocomplete: 'given-name', minlength: '2' });
    constrain('lastName', { autocomplete: 'family-name' });
    constrain('email', { type: 'email', required: '', autocomplete: 'email', inputmode: 'email' });
    constrain('phone', {
      type: 'tel', required: '', autocomplete: 'tel', inputmode: 'numeric',
      pattern: '[6-9][0-9]{9}', maxlength: '10', placeholder: '10-digit mobile'
    });
    /* live digits-only filter on the phone field (mobile friendly) */
    var phoneEl = form.querySelector('[name="phone"]');
    if (phoneEl) {
      phoneEl.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
      }, { passive: true });
    }

    /* ---------------------------------------------------------------
       2. REQUIRED-FIELD VALIDATION — Name, Email, Phone
       --------------------------------------------------------------- */
    function val(name) { var el = form.querySelector('[name="' + name + '"]'); return el ? el.value.trim() : ''; }
    function flag(name, bad) {
      var el = form.querySelector('[name="' + name + '"]'); if (!el) return;
      el.style.borderColor = bad ? '#c0392b' : '';
      el.setAttribute('aria-invalid', bad ? 'true' : 'false');
    }
    function validate() {
      var ok = true;
      var first = val('firstName');
      var email = val('email');
      var phone = val('phone').replace(/\D/g, '');

      if (first.length < 2) { flag('firstName', true); ok = false; } else flag('firstName', false);
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { flag('email', true); ok = false; } else flag('email', false);
      if (!/^[6-9]\d{9}$/.test(phone)) { flag('phone', true); ok = false; } else flag('phone', false);

      if (!ok) {
        var firstBad = form.querySelector('[aria-invalid="true"]');
        if (firstBad) firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return ok;
    }

    /* ---------------------------------------------------------------
       3. 48-HOUR PAYMENT-URGENCY COUNTDOWN
       Persists the deadline per browser so it keeps ticking across reloads.
       Renders into any element with [data-urgency-countdown] (HH:MM:SS).
       --------------------------------------------------------------- */
    (function urgency() {
      var box = document.querySelector('[data-urgency-countdown]');
      if (!box) return;
      var KEY = 'fsiaUrgencyDeadline';
      var deadline = +localStorage.getItem(KEY);
      var now = Date.now();
      if (!deadline || deadline < now) {
        deadline = now + 48 * 60 * 60 * 1000;            /* 48 hours from first view */
        localStorage.setItem(KEY, deadline);
      }
      function pad(n) { return String(n).padStart(2, '0'); }
      function loop() {
        var diff = deadline - Date.now();
        if (diff < 0) diff = 0;
        var h = Math.floor(diff / 3600000),
          m = Math.floor(diff / 60000) % 60,
          s = Math.floor(diff / 1000) % 60;
        box.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        if (diff > 0) requestAnimationFrame(function () { setTimeout(loop, 1000); });
      }
      loop();
    })();

    /* ---------------------------------------------------------------
       4. SUBMIT — validate, persist, route to confirmation dashboard
       --------------------------------------------------------------- */
    function submitForm(event) {
      if (event) event.preventDefault();
      if (!validate()) return false;

      var data = Object.fromEntries(new FormData(form));
      var regId = 'REG' + Date.now();
      var fullName = (data.firstName || '') + ' ' + (data.lastName || '');
      fullName = fullName.trim();

      /* local persistence (success page + payment page read these) */
      try {
        localStorage.setItem('lastRegName', fullName);
        localStorage.setItem('lastRegCategory', CATEGORY);
        localStorage.setItem('lastRegCity', data.city || '');
        localStorage.setItem('lastRegPhone', data.phone || '');
        localStorage.setItem('lastRegEmail', data.email || '');
        if (data.regtype) localStorage.setItem('regtype', data.regtype);
        localStorage.setItem('lastRegId', regId);
        sessionStorage.setItem('fsiaReg', JSON.stringify({
          category: CATEGORY, name: fullName, phone: data.phone || '',
          email: data.email || '', city: data.city || '', state: data.state || '',
          id: regId, timestamp: new Date().toISOString()
        }));
      } catch (e) { /* storage may be blocked — routing still works via querystring */ }

      /* DEVELOPER NOTE: POST `data` (status:'complete') to your backend here, e.g. api/register.php */

      /* secure local routing to the confirmation dashboard */
      var params = new URLSearchParams({
        name: fullName, category: CATEGORY, city: data.city || '',
        phone: data.phone || '', email: data.email || '', id: regId
      });
      window.location.href = 'registration-success.php?' + params.toString();
      return false;
    }

    /* ---------------------------------------------------------------
       5. PHONE VERIFICATION MOCK
       --------------------------------------------------------------- */
    window.verifyPhone = function () {
      var phoneEl = document.getElementById('phone') || form.querySelector('[name="phone"]');
      if (phoneEl && phoneEl.value.replace(/\D/g, '').length === 10) {
        alert("WhatsApp OTP sent to " + phoneEl.value + ". (Verification mock)");

        var phoneGroup = phoneEl.closest('.form-group');
        if (phoneGroup && !document.getElementById('otpGroup')) {
          var otpDiv = document.createElement('div');
          otpDiv.id = 'otpGroup';
          otpDiv.style.marginTop = '1rem';
          otpDiv.innerHTML = '<label>Enter OTP <span class="required">*</span></label>' +
            '<div style="display:flex;gap:10px;">' +
            '<input type="text" id="otpInput" name="otp" maxlength="6" placeholder="6-digit OTP" required style="flex:1;">' +
            '<button type="button" class="btn sm" onclick="confirmOtp()" style="padding:0.5rem 1rem;font-size:0.85rem;border-radius:8px;background:#28a745;border-color:#28a745;">Confirm</button>' +
            '</div>';
          phoneGroup.appendChild(otpDiv);
        }
      } else {
        alert("Please enter a valid 10-digit WhatsApp number first.");
      }
    };

    window.confirmOtp = function () {
      var otpEl = document.getElementById('otpInput');
      if (otpEl && otpEl.value.trim().length >= 4) {
        alert("Phone number verified successfully!");
        var btn = document.querySelector('#otpGroup button');
        if (btn) {
          btn.textContent = "Verified ✓";
          btn.disabled = true;
        }
        otpEl.readOnly = true;
        otpEl.style.borderColor = "#28a745";
        otpEl.style.background = "#f8fff9";
      } else {
        alert("Please enter a valid OTP.");
      }
    };

    /* ---------------------------------------------------------------
       6. STATE & CITY FILTERING
       --------------------------------------------------------------- */
    var stateEl = document.getElementById('state');
    var cityEl = document.getElementById('city');

    if (stateEl && cityEl) {
      var cityMap = {
        "Andhra Pradesh": ["Vijayawada", "Visakhapatnam"],
        "Assam": ["Guwahati"],
        "Bihar": ["Patna"],
        "Chandigarh": ["Chandigarh"],
        "Chhattisgarh": ["Raipur"],
        "Delhi": ["Delhi"],
        "Gujarat": ["Ahmedabad", "Rajkot", "Surat", "Vadodara"],
        "Haryana": ["Faridabad", "Gurgaon"],
        "Jammu and Kashmir": ["Jammu", "Srinagar"],
        "Jharkhand": ["Dhanbad", "Jamshedpur", "Ranchi"],
        "Karnataka": ["Bangalore", "Mangalore", "Mysore"],
        "Kerala": ["Kochi", "Thiruvananthapuram"],
        "Madhya Pradesh": ["Bhopal", "Gwalior", "Indore", "Jabalpur"],
        "Maharashtra": ["Mumbai", "Nagpur", "Nashik", "Navi Mumbai", "Pune", "Thane"],
        "Odisha": ["Bhubaneswar"],
        "Punjab": ["Amritsar", "Jalandhar", "Ludhiana"],
        "Rajasthan": ["Ajmer", "Jaipur", "Jodhpur", "Kota", "Udaipur"],
        "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai"],
        "Telangana": ["Hyderabad"],
        "Uttar Pradesh": ["Agra", "Ghaziabad", "Kanpur", "Lucknow", "Meerut", "Noida", "Varanasi"],
        "Uttarakhand": ["Dehradun"],
        "West Bengal": ["Kolkata"]
      };

      stateEl.addEventListener('change', function () {
        var state = this.value;
        cityEl.innerHTML = '<option value="">Select City</option>';
        if (state && cityMap[state]) {
          cityMap[state].forEach(function (city) {
            var opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            cityEl.appendChild(opt);
          });
        }
        var otherOpt = document.createElement('option');
        otherOpt.value = "Other";
        otherOpt.textContent = "Other";
        cityEl.appendChild(otherOpt);
      });

      // Trigger on load to clear the long hardcoded list until a state is chosen
      if (!stateEl.value) {
        stateEl.dispatchEvent(new Event('change'));
      }
    }

    /* wire up: works whether the form uses onsubmit="submitForm(event)" or not */
    form.addEventListener('submit', submitForm);
    window.submitForm = submitForm;   /* keep inline onclick/onsubmit compatibility */

  });
})();
