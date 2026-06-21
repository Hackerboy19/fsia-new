<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment | Forever Star India</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<meta name="description" content="Secure payment for Forever Star India registration.">
<meta name="robots" content="noindex,follow">
<link rel="canonical" href="https://www.fsia.in/payment.php">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Forever Star India">
<meta property="og:title" content="Payment | Forever Star India">
<meta property="og:description" content="Secure payment for Forever Star India registration.">
<meta property="og:url" content="https://www.fsia.in/payment.php">
<meta property="og:image" content="https://www.fsia.in/uploads/718Step-1.webp">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Payment | Forever Star India">
<meta name="twitter:description" content="Secure payment for Forever Star India registration.">
<meta name="twitter:image" content="https://www.fsia.in/uploads/718Step-1.webp">
<script type="application/ld+json">[{"@context":"https://schema.org","@type":"Organization","name":"Forever Star India","alternateName":"FSIA","url":"https://www.fsia.in/","logo":"https://www.fsia.in/logo.gif","description":"India's biggest platform for beauty pageants and award shows.","sameAs":["https://www.facebook.com/Foreverstarindiaawards/","https://twitter.com/FsiaAward","https://www.instagram.com/fsia_forever/","https://in.pinterest.com/fsiaaward/","https://www.youtube.com/c/foreverstarindiaaward"],"contactPoint":{"@type":"ContactPoint","telephone":"+91-99832-86999","email":"starindiaaward@gmail.com","contactType":"customer service","areaServed":"IN"}},{"@context":"https://schema.org","@type":"WebSite","name":"Forever Star India","url":"https://www.fsia.in/"}]</script>
  <link rel="stylesheet" href="../assets-new/css/main.css">
  <link rel="stylesheet" href="../assets-new/css/pages/payment.css">
  <link rel="stylesheet" href="../assets-new/css/forms-master.css">
</head>
<body>

<?php include '../layout/header1806.php'; ?>

<section class="payment-container">
  <div class="container">
    <div class="payment-card">
      
      <div class="lock-icon">🔒</div>
      <h1 class="payment-title">Secure Payment</h1>
      <p class="payment-subtitle">Complete your registration with a secure payment</p>

      <!-- PAYMENT SUMMARY -->
      <div class="payment-summary">
        <div class="summary-row">
          <span class="summary-label">Registration Type:</span>
          <span class="summary-value" id="regType">Forever Miss India 2026</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Registration ID:</span>
          <span class="summary-value" id="regId">REG1234567890</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Registration Fee:</span>
          <span class="summary-value">₹1,999</span>
        </div>
      </div>

      <!-- AMOUNT BOX -->
      <div class="amount-box">
        <div class="amount-label">Total Amount Payable</div>
        <div class="amount-value">₹1,999</div>
        <p style="margin-top:1rem;font-size:.9rem;opacity:.9">One-time payment to secure your spot</p>
      </div>

      <!-- PAYMENT METHODS -->
      <div class="payment-methods">
        <h3>Select Payment Method</h3>
        <div class="method-grid">
          <div class="payment-method selected" onclick="selectMethod(this,'upi')">
            <div class="method-icon">📱</div>
            <div class="method-name">UPI</div>
            <div class="method-desc">Google Pay, PhonePe, Paytm</div>
          </div>
          <div class="payment-method" onclick="selectMethod(this,'card')">
            <div class="method-icon">💳</div>
            <div class="method-name">Credit/Debit Card</div>
            <div class="method-desc">Visa, Mastercard, RuPay</div>
          </div>
          <div class="payment-method" onclick="selectMethod(this,'netbanking')">
            <div class="method-icon">🏦</div>
            <div class="method-name">Net Banking</div>
            <div class="method-desc">All major banks</div>
          </div>
          <div class="payment-method" onclick="selectMethod(this,'wallet')">
            <div class="method-icon">💰</div>
            <div class="method-name">Digital Wallet</div>
            <div class="method-desc">Amazon Pay, Airtel</div>
          </div>
        </div>
      </div>

      <!-- PAYMENT FORM -->
      <form id="paymentForm" onsubmit="processPayment(event)">
        
        <div id="upiFields" style="display:block">
          <div class="form-group">
            <label>UPI ID <span style="color:var(--crimson)">*</span></label>
            <input type="text" id="upiId" placeholder="yourname@upi" name="upi">
          </div>
        </div>

        <div id="cardFields" style="display:none">
          <div class="form-group">
            <label>Full Name on Card <span style="color:var(--crimson)">*</span></label>
            <input type="text" id="cardName" placeholder="John Doe">
          </div>
          <div class="form-group">
            <label>Card Number <span style="color:var(--crimson)">*</span></label>
            <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Expiry Date <span style="color:var(--crimson)">*</span></label>
              <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5">
            </div>
            <div class="form-group">
              <label>CVV <span style="color:var(--crimson)">*</span></label>
              <input type="text" id="cardCvv" placeholder="123" maxlength="3">
            </div>
          </div>
        </div>

        <div id="netBankingFields" style="display:none">
          <div class="form-group">
            <label>Select Your Bank <span style="color:var(--crimson)">*</span></label>
            <select style="width:100%;padding:1rem;border:2px solid #e9ecef;border-radius:8px;font-family:'Poppins',sans-serif;font-size:.95rem">
              <option>Choose bank</option>
              <option>State Bank of India</option>
              <option>HDFC Bank</option>
              <option>ICICI Bank</option>
              <option>Axis Bank</option>
              <option>Other Banks</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Email Address <span style="color:var(--crimson)">*</span></label>
          <input type="email" id="paymentEmail" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label>Phone Number <span style="color:var(--crimson)">*</span></label>
          <input type="tel" id="paymentPhone" placeholder="9999999999" maxlength="10" required>
        </div>

        <!-- SECURITY INFO -->
        <div class="security-info">
          <p>🔒 <strong>Your payment is 100% secure</strong></p>
          <p>We use industry-leading encryption to protect your payment information. Your data is never shared with third parties.</p>
        </div>

        <!-- TERMS -->
        <div class="form-group">
          <label>
            <input type="checkbox" id="agreeTerms" required style="width:auto;margin-right:.5rem">
            I agree to the payment terms and registration policy <span style="color:var(--crimson)">*</span>
          </label>
        </div>

        <button type="submit" class="cta-button">Complete Payment - ₹1,999</button>
      </form>

      <!-- PAYMENT GUARANTEE -->
      <div class="payment-guarantee">
        <div class="guarantee-icon">✓</div>
        <p><strong>30-Day Money Back Guarantee</strong></p>
        <p>Not satisfied? Get 100% refund within 30 days, no questions asked.</p>
      </div>

      <!-- HELP -->
      <div style="background:#f0f4f8;border-radius:12px;padding:1.5rem;margin-top:2rem;text-align:center">
        <p style="color:var(--ink-soft);margin-bottom:1rem"><strong>Payment Issues?</strong></p>
        <p><a href="tel:+919983286999" style="color:var(--crimson);font-weight:700">Call: +91-99832-86999</a></p>
        <p><a href="https://wa.me/919983286999" style="color:var(--crimson);font-weight:700">WhatsApp Support</a></p>
      </div>

    </div>
  </div>
</section>

<?php include '../layout/footer1806.php'; ?>

<script src="../assets-new/js/main.js"></script>
<script src="../assets-new/js/forms-handler.js"></script>
</body>
</html>
