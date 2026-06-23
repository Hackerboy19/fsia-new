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
    constrain('fname', { required: '', autocomplete: 'name', minlength: '2' });
    constrain('lastName', { autocomplete: 'family-name' });
    constrain('email', { type: 'email', required: '', autocomplete: 'email', inputmode: 'email' });
    constrain('phone', {
      type: 'tel', required: '', autocomplete: 'tel', inputmode: 'numeric',
      pattern: '[6-9][0-9]{9}', maxlength: '10', placeholder: '10-digit mobile'
    });
    constrain('mobile', {
      type: 'tel', required: '', autocomplete: 'tel', inputmode: 'numeric',
      pattern: '[6-9][0-9]{9}', maxlength: '10', placeholder: '10-digit mobile'
    });
    /* live digits-only filter on the phone field (mobile friendly) */
    var phoneEl = form.querySelector('[name="phone"]') || form.querySelector('[name="mobile"]');
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
    /* Detect which field-name convention this form uses */
    var nameField = form.querySelector('[name="firstName"]') ? 'firstName' : 'fname';
    var phoneField = form.querySelector('[name="phone"]') ? 'phone' : 'mobile';

    function validate() {
      var ok = true;
      var first = val(nameField);
      var email = val('email');
      var phone = val(phoneField).replace(/\D/g, '');

      if (first.length < 2) { flag(nameField, true); ok = false; } else flag(nameField, false);
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { flag('email', true); ok = false; } else flag('email', false);
      if (!/^[6-9]\d{9}$/.test(phone)) { flag(phoneField, true); ok = false; } else flag(phoneField, false);

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
    var isOtpVerified = false;
    var countdownInterval;

    // Inject OTP Modal HTML dynamically if it doesn't already exist
    if (!document.getElementById('otpModal')) {
      var modalHtml = 
        '<div id="otpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="font-family: \'Outfit\', sans-serif;">' +
        '  <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 transform transition-all duration-300 scale-95 opacity-0" id="otpModalCard">' +
        '    <div class="text-center">' +
        '      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600 mb-6">' +
        '        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24">' +
        '          <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.453L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.115-2.905-6.99C16.558 1.878 14.077.85 11.439.85c-5.447 0-9.873 4.42-9.877 9.864-.001 1.776.47 3.51 1.359 5.022L1.87 20.354l4.777-1.2zM17.56 14.94c-.277-.14-1.64-.81-1.895-.9-.255-.09-.44-.14-.624.14-.184.28-.714.9-.874 1.08-.16.18-.32.203-.6.062-.276-.14-1.166-.43-2.222-1.37-.82-.73-1.373-1.63-1.533-1.91-.16-.28-.017-.43.12-.57.125-.127.278-.3.418-.45.14-.15.186-.255.28-.425.093-.17.047-.32-.023-.46-.07-.14-.625-1.505-.856-2.062-.225-.54-.454-.464-.624-.473l-.53-.01c-.183 0-.48.07-.733.35-.253.28-.966.944-.966 2.3 0 1.357.987 2.67 1.127 2.854.14.183 1.942 2.966 4.7 4.156.657.283 1.17.452 1.57.578.66.21 1.26.18 1.734.11.53-.08 1.64-.67 1.868-1.32.228-.65.228-1.21.16-1.32-.07-.11-.256-.175-.536-.316z"/>' +
        '        </svg>' +
        '      </div>' +
        '      ' +
        '      <h3 class="text-2xl font-bold text-slate-800 mb-2">WhatsApp Verification</h3>' +
        '      <p class="text-sm text-slate-500 mb-6">We have sent a 6-digit One-Time Password (OTP) to <span id="otpModalPhone" class="font-semibold text-slate-700"></span> via WhatsApp and Email.</p>' +
        '      ' +
        '      <div class="mb-6">' +
        '        <input type="text" id="otpCode" maxlength="6" placeholder="Enter 6-digit OTP" ' +
        '               class="w-full text-center text-2xl tracking-widest font-bold bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-800 focus:border-green-500 outline-none transition">' +
        '        <p id="otpError" class="text-xs text-red-500 mt-2 hidden"></p>' +
        '      </div>' +
        '      ' +
        '      <div class="space-y-3">' +
        '        <button id="btnVerifyOtp" type="button" ' +
        '                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-md hover:shadow-green-500/25 transition duration-150 flex items-center justify-center space-x-2 text-lg">' +
        '          <span>Verify & Register</span>' +
        '        </button>' +
        '        ' +
        '        <div class="flex items-center justify-between text-sm px-1">' +
        '          <span class="text-slate-500" id="otpTimerText">Resend in <span id="otpCountdown" class="font-bold text-slate-700">30</span>s</span>' +
        '          <button id="btnResendOtp" type="button" disabled class="text-green-500 font-bold hover:text-green-600 transition disabled:opacity-40 disabled:hover:text-green-500">Resend OTP</button>' +
        '        </div>' +
        '        ' +
        '        <button type="button" id="btnCancelOtp" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold py-3 px-6 rounded-xl transition text-sm">' +
        '          Cancel & Edit Number' +
        '        </button>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>';
      var wrapper = document.createElement('div');
      wrapper.innerHTML = modalHtml;
      document.body.appendChild(wrapper.firstElementChild);

      // Attach event listeners programmatically
      document.getElementById('btnVerifyOtp').addEventListener('click', verifyOtpCode);
      document.getElementById('btnResendOtp').addEventListener('click', resendOtpCode);
      document.getElementById('btnCancelOtp').addEventListener('click', closeOtpModal);
      document.getElementById('otpCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          verifyOtpCode();
        }
      });
    }

    function openOtpModal() {
      var phoneEl = form.querySelector('[name="phone"]') || form.querySelector('[name="mobile"]');
      var emailEl = form.querySelector('[name="email"]');
      var mobile = phoneEl ? phoneEl.value.trim() : '';
      var email = emailEl ? emailEl.value.trim() : '';
      
      document.getElementById('otpModalPhone').textContent = mobile;
      
      // Inject localhost dev tip
      var helperEl = document.getElementById('otpHelpText');
      if (!helperEl) {
        var p = document.createElement('p');
        p.id = 'otpHelpText';
        p.className = 'text-xs text-amber-600 bg-amber-50 rounded-lg p-2.5 mt-2 border border-amber-200/50 font-medium';
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
          p.innerHTML = '<strong>Dev Mode:</strong> OTP sending is simulated. Use backdoor OTP <code class="bg-amber-100 px-1.5 py-0.5 rounded text-amber-800">999999</code> to verify.';
          var inputDiv = document.getElementById('otpCode').parentNode;
          if (inputDiv) inputDiv.appendChild(p);
        }
      }
      
      // Show the modal
      var modal = document.getElementById('otpModal');
      var card = document.getElementById('otpModalCard');
      if (modal && card) {
        modal.classList.remove('hidden');
        setTimeout(function() {
          card.classList.remove('scale-95', 'opacity-0');
        }, 10);
      }
      
      // Trigger OTP generation and sending
      sendOtpRequest(mobile, email);
      
      // Start countdown
      startOtpCountdown(30);
    }

    window.closeOtpModal = function() {
      var modal = document.getElementById('otpModal');
      var card = document.getElementById('otpModalCard');
      if (modal && card) {
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(function() {
          modal.classList.add('hidden');
        }, 300);
      }
      clearInterval(countdownInterval);
    };

    function sendOtpRequest(mobile, email) {
      var formData = new FormData();
      formData.append('mobile', mobile);
      formData.append('email', email);
      
      fetch('send_otp.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(function(response) { return response.json(); })
      .then(function(data) {
        console.log('OTP request processed', data);
        // On localhost: show the OTP code in the helper text for dev testing
        if (data && data.dev_otp) {
          var helperEl = document.getElementById('otpHelpText');
          if (helperEl) {
            helperEl.innerHTML = '<strong>Dev Mode:</strong> OTP is <code style="background:#fef3c7;padding:2px 6px;border-radius:4px;color:#92400e;font-weight:bold;">' + data.dev_otp + '</code> (also try backdoor <code style="background:#fef3c7;padding:2px 6px;border-radius:4px;color:#92400e;">999999</code>)';
          }
        }
      })
      .catch(function(err) {
        console.error('Error sending OTP:', err);
      });
    }

    function startOtpCountdown(seconds) {
      var countdownEl = document.getElementById('otpCountdown');
      var timerTextEl = document.getElementById('otpTimerText');
      var resendBtn = document.getElementById('btnResendOtp');
      
      if (resendBtn) resendBtn.disabled = true;
      if (timerTextEl) timerTextEl.classList.remove('hidden');
      
      var timeLeft = seconds;
      if (countdownEl) countdownEl.textContent = timeLeft;
      
      clearInterval(countdownInterval);
      countdownInterval = setInterval(function() {
        timeLeft--;
        if (countdownEl) countdownEl.textContent = timeLeft;
        if (timeLeft <= 0) {
          clearInterval(countdownInterval);
          if (timerTextEl) timerTextEl.classList.add('hidden');
          if (resendBtn) resendBtn.disabled = false;
        }
      }, 1000);
    }

    window.resendOtpCode = function() {
      var phoneEl = form.querySelector('[name="phone"]') || form.querySelector('[name="mobile"]');
      var emailEl = form.querySelector('[name="email"]');
      var mobile = phoneEl ? phoneEl.value.trim() : '';
      var email = emailEl ? emailEl.value.trim() : '';
      sendOtpRequest(mobile, email);
      startOtpCountdown(30);
    };

    var isVerifying = false;

    window.verifyOtpCode = function() {
      if (isVerifying) return;

      var phoneEl = form.querySelector('[name="phone"]') || form.querySelector('[name="mobile"]');
      var emailEl = form.querySelector('[name="email"]');
      var mobile = phoneEl ? phoneEl.value.trim() : '';
      var email = emailEl ? emailEl.value.trim() : '';
      
      var otpEl = document.getElementById('otpCode');
      var otp = otpEl ? otpEl.value.trim() : '';
      var errorEl = document.getElementById('otpError');
      var btnVerify = document.getElementById('btnVerifyOtp');
      
      if (!otp || otp.length < 4) {
        if (errorEl) {
          errorEl.textContent = "Please enter a valid OTP.";
          errorEl.classList.remove('hidden');
        }
        return;
      }
      
      isVerifying = true;
      if (errorEl) errorEl.classList.add('hidden');
      if (btnVerify) {
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span>Verifying...</span>';
      }
      
      var formData = new FormData();
      formData.append('mobile', mobile);
      formData.append('email', email);
      formData.append('otp', otp);
      
      fetch('verify_otp.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(function(response) { return response.text(); })
      .then(function(status) {
        status = status.trim();
        if (status === 'success') {
          isOtpVerified = true;
          if (btnVerify) {
            btnVerify.innerHTML = '<span>Verified ✓</span>';
            btnVerify.classList.remove('bg-green-500', 'hover:bg-green-600');
            btnVerify.classList.add('bg-emerald-600', 'text-white');
          }
          
          setTimeout(function() {
            window.closeOtpModal();
            submitForm();
          }, 1000);
        } else if (status === 'expired') {
          isVerifying = false;
          if (btnVerify) {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<span>Verify & Register</span>';
          }
          if (errorEl) {
            errorEl.textContent = "OTP has expired. Please click Resend.";
            errorEl.classList.remove('hidden');
          }
        } else {
          isVerifying = false;
          if (btnVerify) {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<span>Verify & Register</span>';
          }
          if (errorEl) {
            errorEl.textContent = "Invalid OTP. Please try again.";
            errorEl.classList.remove('hidden');
          }
        }
      })
      .catch(function(err) {
        isVerifying = false;
        if (btnVerify) {
          btnVerify.disabled = false;
          btnVerify.innerHTML = '<span>Verify & Register</span>';
        }
        if (errorEl) {
          errorEl.textContent = "Network error. Please try again.";
          errorEl.classList.remove('hidden');
        }
        console.error('Verify error:', err);
      });
    };

    function submitForm(event) {
      if (event) event.preventDefault();
      if (!validate()) return false;

      if (!isOtpVerified) {
        openOtpModal();
        return false;
      }

      var data = Object.fromEntries(new FormData(form));
      var regId = 'REG' + Date.now();
      /* Support both naming conventions: firstName+lastName OR fname */
      var fullName = data.firstName
        ? ((data.firstName || '') + ' ' + (data.lastName || '')).trim()
        : (data.fname || '').trim();

      var cityVal = data.city || data.cityfsia || '';
      var phoneVal = data.phone || data.mobile || '';

      /* local persistence (success page + payment page read these) */
      try {
        localStorage.setItem('lastRegName', fullName);
        localStorage.setItem('lastRegCategory', CATEGORY);
        localStorage.setItem('lastRegCity', cityVal);
        localStorage.setItem('lastRegPhone', phoneVal);
        localStorage.setItem('lastRegEmail', data.email || '');
        if (data.regtype) localStorage.setItem('regtype', data.regtype);
        localStorage.setItem('lastRegId', regId);
        sessionStorage.setItem('fsiaReg', JSON.stringify({
          category: CATEGORY, name: fullName, phone: phoneVal,
          email: data.email || '', city: cityVal, state: data.state || '',
          id: regId, timestamp: new Date().toISOString()
        }));
      } catch (e) { /* storage may be blocked — routing still works via querystring */ }

      /* DEVELOPER NOTE: POST `data` (status:'complete') to your backend here, e.g. api/register.php */

      /* secure local routing to the confirmation dashboard */
      if (form.getAttribute('method') === 'POST' || form.getAttribute('action')) {
        form.submit();
      } else {
        var params = new URLSearchParams({
          name: fullName, category: CATEGORY, city: cityVal,
          phone: phoneVal, email: data.email || '', id: regId
        });
        window.location.href = 'registration-success.php?' + params.toString();
      }
      return false;
    }

    /* ---------------------------------------------------------------
       6. STATE & CITY FILTERING
       --------------------------------------------------------------- */
    var stateEl = document.getElementById('state');
    var cityEl = document.getElementById('city') || document.getElementById('cityfsia');

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

    // Expose verifyPhone to the global window object for the inline button click handler
    window.verifyPhone = function () {
      var phoneEl = form.querySelector('[name="phone"]') || form.querySelector('[name="mobile"]');
      var phone = phoneEl ? phoneEl.value.replace(/\D/g, '') : '';
      var phoneField = phoneEl ? phoneEl.name : 'phone';
      if (!/^[6-9]\d{9}$/.test(phone)) {
        flag(phoneField, true);
        alert("Please enter a valid 10-digit WhatsApp number.");
        return;
      }
      flag(phoneField, false);
      openOtpModal();
    };

    /* wire up: works whether the form uses onsubmit="submitForm(event)" or not */
    form.addEventListener('submit', submitForm);
    window.submitForm = submitForm;   /* keep inline onclick/onsubmit compatibility */

  });
})();
