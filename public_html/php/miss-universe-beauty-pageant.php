<?php
include("config.php");

$year = date("Y");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reg'])) {
    $fname = trim($_POST['fname'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $cityfsia = trim($_POST['cityfsia'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $regtype = trim($_POST['regtype'] ?? '30');
    $age = trim($_POST['age'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($fname) && !empty($mobile) && !empty($email) && !empty($state) && !empty($cityfsia)) {
        if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true || ($_SESSION['rmob'] ?? '') !== $mobile) {
            $error = "Mobile number verification is required. Please verify via WhatsApp OTP.";
        } else {
            unset($_SESSION['otp_verified']);
            unset($_SESSION['rmob']);

            if ($connect) {
                $fname_esc = mysqli_real_escape_string($connect, $fname);
                $mobile_esc = mysqli_real_escape_string($connect, $mobile);
                $email_esc = mysqli_real_escape_string($connect, $email);
                $state_esc = mysqli_real_escape_string($connect, $state);
                $cityfsia_esc = mysqli_real_escape_string($connect, $cityfsia);
                $instagram_esc = mysqli_real_escape_string($connect, $instagram);
                $regtype_esc = mysqli_real_escape_string($connect, $regtype);
                $age_esc = !empty($age) ? (int)$age : "NULL";
                $dob_esc = !empty($dob) ? "'" . mysqli_real_escape_string($connect, $dob) . "'" : "NULL";
                $qualification_esc = mysqli_real_escape_string($connect, $qualification);
                $skills_esc = mysqli_real_escape_string($connect, $skills);
                $message_esc = mysqli_real_escape_string($connect, $message);

                $sql = "INSERT INTO registration (first_name, email, mobile, state, cityfsia, instagram, regtype, age, dob, qualification, skills, message, payment_status)
                        VALUES ('$fname_esc', '$email_esc', '$mobile_esc', '$state_esc', '$cityfsia_esc', '$instagram_esc', '$regtype_esc', $age_esc, $dob_esc, '$qualification_esc', '$skills_esc', '$message_esc', 'pending')";
                if (mysqli_query($connect, $sql)) {
                    $insert_id = mysqli_insert_id($connect);
                    $token = md5($insert_id);
                    header("Location: registration-success.php?token=" . $token);
                    exit;
                } else {
                    $error = "Database error: " . mysqli_error($connect);
                }
            } else {
                // Fallback mock session database
                $insert_id = count($_SESSION['mock_db']) + 1;
                $row = [
                    'id' => $insert_id,
                    'first_name' => $fname,
                    'email' => $email,
                    'mobile' => $mobile,
                    'state' => $state,
                    'cityfsia' => $cityfsia,
                    'instagram' => $instagram,
                    'regtype' => $regtype,
                    'age' => $age,
                    'dob' => $dob,
                    'qualification' => $qualification,
                    'skills' => $skills,
                    'message' => $message,
                    'trans_id' => null,
                    'payment_status' => 'pending',
                    'pdate' => null
                ];
                $_SESSION['mock_db'][$insert_id] = $row;
                $token = md5($insert_id);
                header("Location: registration-success.php?token=" . $token);
                exit;
            }
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Miss Universe by Forever Star India 2026 Registration | Forever Star India</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<meta name="description" content="Register for Miss Universe by Forever Star India 2026 with Forever Star India (FSIA). India's biggest beauty pageant platform. Apply online — auditions & nominations open from every city of India.">
<meta name="robots" content="index,follow">
<link rel="canonical" href="https://www.fsia.in/miss-universe-beauty-pageant.php">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Forever Star India">
<meta property="og:title" content="Miss Universe by Forever Star India 2026 Registration | Forever Star India">
<meta property="og:description" content="Register for Miss Universe by Forever Star India 2026 with Forever Star India (FSIA). India's biggest beauty pageant platform. Apply online — auditions & nominations open from every city of India.">
<meta property="og:url" content="https://www.fsia.in/miss-universe-beauty-pageant.php">
<meta property="og:image" content="https://www.fsia.in/uploads/718Step-1.webp">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Miss Universe by Forever Star India 2026 Registration | Forever Star India">
<meta name="twitter:description" content="Register for Miss Universe by Forever Star India 2026 with Forever Star India (FSIA). India's biggest beauty pageant platform. Apply online — auditions & nominations open from every city of India.">
<meta name="twitter:image" content="https://www.fsia.in/uploads/718Step-1.webp">
<script type="application/ld+json">[{"@context":"https://schema.org","@type":"Organization","name":"Forever Star India","alternateName":"FSIA","url":"https://www.fsia.in/","logo":"https://www.fsia.in/logo.gif","description":"India's biggest platform for beauty pageants and award shows.","sameAs":["https://www.facebook.com/Foreverstarindiaawards/","https://twitter.com/FsiaAward","https://www.instagram.com/fsia_forever/","https://in.pinterest.com/fsiaaward/","https://www.youtube.com/c/foreverstarindiaaward"],"contactPoint":{"@type":"ContactPoint","telephone":"+91-99832-86999","email":"starindiaaward@gmail.com","contactType":"customer service","areaServed":"IN"}},{"@context":"https://schema.org","@type":"WebSite","name":"Forever Star India","url":"https://www.fsia.in/"}]</script>
  <link rel="stylesheet" href="assets-new/css/main.css">
  <link rel="stylesheet" href="assets-new/css/forms-master.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'header1806.php'; ?>

<section class="hero">
  <div class="container">
    <div class="hero-content">
      <div class="crown-scene">
  <div class="crown-stage">
    <div class="crown-wrap">
      <img class="crown-img"
           src="../assets-new/media/banners/crown-forever.jpg"
           alt="The FOREVER Crown — Miss Universe 2026"
           loading="eager"
           onerror="this.closest('.crown-scene').style.display='none'">
      <div class="crown-shine"></div>
      <div class="crown-glow"></div>
      <div class="crown-pulse"></div>
      <div class="crown-sparks">
        <span class="spark" style="left:15%;top:70%;--dur:3.8s;--del:0s;  --rise:-80px"></span>
        <span class="spark" style="left:28%;top:60%;--dur:4.4s;--del:.6s; --rise:-70px;width:3px;height:3px"></span>
        <span class="spark" style="left:42%;top:50%;--dur:3.2s;--del:.2s; --rise:-90px"></span>
        <span class="spark" style="left:55%;top:55%;--dur:5.0s;--del:1.0s;--rise:-75px;width:3px;height:3px"></span>
        <span class="spark" style="left:68%;top:65%;--dur:3.6s;--del:.4s; --rise:-85px"></span>
        <span class="spark" style="left:80%;top:58%;--dur:4.1s;--del:.8s; --rise:-68px;width:3px;height:3px"></span>
        <span class="spark" style="left:22%;top:40%;--dur:4.8s;--del:1.4s;--rise:-60px"></span>
        <span class="spark" style="left:75%;top:42%;--dur:3.4s;--del:.3s; --rise:-78px"></span>
        <span class="spark" style="left:50%;top:30%;--dur:4.6s;--del:.9s; --rise:-55px;width:3px;height:3px"></span>
        <span class="spark" style="left:35%;top:75%;--dur:3.9s;--del:1.2s;--rise:-82px"></span>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>
</section>

<section class="form-section py-12 px-4 bg-slate-100/50">
  <div class="max-w-4xl mx-auto">
    
    <!-- Visual Header Notice -->
    <div class="bg-amber-500/10 border border-amber-500/20 text-amber-900 rounded-2xl p-5 mb-8 text-center text-sm md:text-base font-semibold shadow-sm">
      Welcome to our International Platform. Apply online — auditions & nominations open from every city of India.
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-slate-100">
      
      <!-- Status Indicator Card Layout -->
      <div class="md:col-span-4 bg-gradient-to-b from-amber-500 to-amber-600 p-8 flex flex-col justify-between text-slate-950">
        <div>
          <div class="flex items-center space-x-2 font-bold mb-6">
            <span class="h-2.5 w-2.5 rounded-full bg-slate-950 animate-ping"></span>
            <span class="text-xs uppercase tracking-widest font-mono">Step 1 of 4</span>
          </div>
          
          <!-- 4-line status indicator -->
          <div class="space-y-5 text-sm font-medium">
            <div class="flex items-start space-x-3">
              <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-950 text-white flex items-center justify-center text-xs font-bold font-mono">1</span>
              <p class="leading-relaxed">You have arrived at the first step of your registration process.</p>
            </div>
            <div class="flex items-start space-x-3">
              <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-950/20 text-slate-950 flex items-center justify-center text-xs font-bold font-mono">2</span>
              <p class="leading-relaxed">To proceed to the audition after this step,</p>
            </div>
            <div class="flex items-start space-x-3 text-slate-900/90">
              <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-950/20 text-slate-950 flex items-center justify-center text-xs font-bold font-mono">3</span>
              <p class="leading-relaxed">Enjoy a special discounted registration fee of 
                <span class="line-through text-slate-500">₹5,999</span> 
                <span class="font-semibold text-emerald-600">₹2,999</span> 
                once your application is accepted.</p>
            </div>
          </div>
        </div>
        
        <div class="mt-8 pt-4 border-t border-slate-950/10 text-xs font-semibold text-slate-950/80">
          Forever Star India Beauty Pageants 2026
        </div>
      </div>

      <!-- Registration Form inputs -->
      <div class="md:col-span-8 p-8 md:p-10 bg-slate-50">
        
        <?php if (!empty($error)): ?>
          <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            ⚠️ <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form action="" method="POST" id="pageantForm" class="space-y-5">
          <input type="hidden" name="regtype" value="30">
          <input type="hidden" name="submit_reg" value="1">

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="fname">
              Full Name <span class="text-amber-500">*</span>
            </label>
            <input type="text" name="fname" id="fname" required
                   placeholder="Enter your full name" 
                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="age">
                Age
              </label>
              <select name="age" id="age"
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                <option value="">Select Age</option>
                <?php for($i=18; $i<=35; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="dob">
                Date of Birth
              </label>
              <input type="date" name="dob" id="dob"
                     class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="state">
                State <span class="text-amber-500">*</span>
              </label>
              <select name="state" id="state" required
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-850 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                <option value="">Select State</option>
                <option value="Delhi">Delhi</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="West Bengal">West Bengal</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="cityfsia">
                City <span class="text-amber-500">*</span>
              </label>
              <select name="cityfsia" id="cityfsia" required
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-855 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                <option value="">Select City</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="mobile">
                WhatsApp Mobile Number <span class="text-amber-500">*</span>
              </label>
              <input type="tel" name="mobile" id="mobile" required
                     placeholder="10-digit mobile" pattern="[6-9][0-9]{9}" maxlength="10"
                     class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="email">
                Email Address <span class="text-amber-500">*</span>
              </label>
              <input type="email" name="email" id="email" required
                     placeholder="name@email.com" 
                     class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="qualification">
                Highest Qualification
              </label>
              <select name="qualification" id="qualification"
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                <option value="">Select Qualification</option>
                <option value="10th Pass">10th Pass</option>
                <option value="12th Pass">12th Pass</option>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Postgraduate">Postgraduate</option>
                <option value="Doctorate">Doctorate</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="skills">
                Special Skills / Talents
              </label>
              <input type="text" name="skills" id="skills"
                     placeholder="e.g. Dance, Singing, Acting" 
                     class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="instagram">
              Instagram Handle
            </label>
            <input type="text" name="instagram" id="instagram"
                   placeholder="@your_profile" 
                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="message">
              Tell us about yourself
            </label>
            <textarea name="message" id="message" rows="3"
                      placeholder="Share your dreams, achievements, and why you want to register..." 
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm"></textarea>
          </div>

          <div class="flex items-start space-x-2 pt-2">
            <input type="checkbox" name="agree" id="agree" checked required
                   class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer mt-1">
            <label for="agree" class="text-xs text-slate-500 leading-relaxed cursor-pointer select-none">
              I agree to the <a href="termscondition.php" target="_blank" class="text-amber-500 underline font-medium">terms & conditions</a> and <a href="privacy-policy.php" target="_blank" class="text-amber-500 underline font-medium">privacy policy</a>
            </label>
          </div>

          <div class="pt-4">
            <button type="submit" 
                    class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-4 px-6 rounded-xl shadow-md hover:shadow-amber-500/25 transition duration-150 flex items-center justify-center space-x-2 text-lg">
              <span>Register Now</span>
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>


<?php include 'footer1806.php'; ?>

<!-- OTP Verification Modal -->
<div id="otpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 transform transition-all duration-300 scale-95 opacity-0" id="otpModalCard">
    <div class="text-center">
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600 mb-6">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24">
          <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.453L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.115-2.905-6.99C16.558 1.878 14.077.85 11.439.85c-5.447 0-9.873 4.42-9.877 9.864-.001 1.776.47 3.51 1.359 5.022L1.87 20.354l4.777-1.2zM17.56 14.94c-.277-.14-1.64-.81-1.895-.9-.255-.09-.44-.14-.624.14-.184.28-.714.9-.874 1.08-.16.18-.32.203-.6.062-.276-.14-1.166-.43-2.222-1.37-.82-.73-1.373-1.63-1.533-1.91-.16-.28-.017-.43.12-.57.125-.127.278-.3.418-.45.14-.15.186-.255.28-.425.093-.17.047-.32-.023-.46-.07-.14-.625-1.505-.856-2.062-.225-.54-.454-.464-.624-.473l-.53-.01c-.183 0-.48.07-.733.35-.253.28-.966.944-.966 2.3 0 1.357.987 2.67 1.127 2.854.14.183 1.942 2.966 4.7 4.156.657.283 1.17.452 1.57.578.66.21 1.26.18 1.734.11.53-.08 1.64-.67 1.868-1.32.228-.65.228-1.21.16-1.32-.07-.11-.256-.175-.536-.316z"/>
        </svg>
      </div>
      
      <h3 class="text-2xl font-bold text-slate-800 mb-2">WhatsApp Verification</h3>
      <p class="text-sm text-slate-500 mb-6">We have sent a 6-digit One-Time Password (OTP) to <span id="otpModalPhone" class="font-semibold text-slate-700"></span> via WhatsApp and Email.</p>
      
      <div class="mb-6">
        <input type="text" id="otpCode" maxlength="6" placeholder="Enter 6-digit OTP" 
               class="w-full text-center text-2xl tracking-widest font-bold bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-800 focus:border-green-500 outline-none transition">
        <p id="otpError" class="text-xs text-red-500 mt-2 hidden"></p>
      </div>
      
      <div class="space-y-3">
        <button id="btnVerifyOtp" onclick="verifyOtpCode()" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-md hover:shadow-green-500/25 transition duration-150 flex items-center justify-center space-x-2 text-lg">
          <span>Verify & Register</span>
        </button>
        
        <div class="flex items-center justify-between text-sm px-1">
          <span class="text-slate-500" id="otpTimerText">Resend in <span id="otpCountdown" class="font-bold text-slate-700">30</span>s</span>
          <button id="btnResendOtp" onclick="resendOtpCode()" disabled class="text-green-500 font-bold hover:text-green-600 transition disabled:opacity-40 disabled:hover:text-green-500">Resend OTP</button>
        </div>
        
        <button type="button" onclick="closeOtpModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold py-3 px-6 rounded-xl transition text-sm">
          Cancel & Edit Number
        </button>
      </div>
    </div>
  </div>
</div>


<a href="tel:+919983286999" class="contact-manager-bar">Contact Account Manager</a>
<script src="assets-new/js/main.js"></script>
<script src="assets-new/js/forms-handler.js"></script>
  <!-- Dynamic State-to-City mapping logic & OTP verification script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const cityMap = {
        "Delhi": ["New Delhi", "Dwarka", "Rohini"],
        "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Thane", "Nashik"],
        "Rajasthan": ["Jaipur", "Jodhpur", "Udaipur", "Kota", "Ajmer"],
        "Karnataka": ["Bangalore", "Mysore", "Hubli", "Mangalore"],
        "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot"],
        "Uttar Pradesh": ["Noida", "Lucknow", "Ghaziabad", "Agra", "Varanasi"],
        "West Bengal": ["Kolkata", "Howrah", "Darjeeling"],
        "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai"]
      };

      const stateSelect = document.getElementById('state');
      const citySelect = document.getElementById('cityfsia');

      stateSelect.addEventListener('change', function() {
        const state = this.value;
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        if (state && cityMap[state]) {
          cityMap[state].forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            citySelect.appendChild(opt);
          });
        }
        
        const otherOpt = document.createElement('option');
        otherOpt.value = "Other";
        otherOpt.textContent = "Other";
        citySelect.appendChild(otherOpt);
      });

      // OTP Verification Logic
      let isOtpVerified = false;
      let countdownInterval;
      const form = document.getElementById('pageantForm');

      form.addEventListener('submit', function(e) {
        if (isOtpVerified) {
          return true; // allow normal submission
        }
        
        e.preventDefault();
        
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }
        
        openOtpModal();
      });

      function openOtpModal() {
        const mobile = document.getElementById('mobile').value;
        const email = document.getElementById('email').value;
        
        document.getElementById('otpModalPhone').textContent = mobile;
        
        // Show the modal
        const modal = document.getElementById('otpModal');
        const card = document.getElementById('otpModalCard');
        modal.classList.remove('hidden');
        setTimeout(() => {
          card.classList.remove('scale-95', 'opacity-0');
        }, 10);
        
        // Trigger OTP generation and sending
        sendOtpRequest(mobile, email);
        
        // Start countdown
        startOtpCountdown(30);
      }

      window.closeOtpModal = function() {
        const modal = document.getElementById('otpModal');
        const card = document.getElementById('otpModalCard');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
          modal.classList.add('hidden');
        }, 300);
        clearInterval(countdownInterval);
      };

      function sendOtpRequest(mobile, email) {
        const formData = new FormData();
        formData.append('mobile', mobile);
        formData.append('email', email);
        
        fetch('send_otp.php', {
          method: 'POST',
          body: formData,
          credentials: 'include'
        })
        .then(response => {
          console.log('OTP request processed');
        })
        .catch(err => {
          console.error('Error sending OTP:', err);
        });
      }

      function startOtpCountdown(seconds) {
        const countdownEl = document.getElementById('otpCountdown');
        const timerTextEl = document.getElementById('otpTimerText');
        const resendBtn = document.getElementById('btnResendOtp');
        
        resendBtn.disabled = true;
        timerTextEl.classList.remove('hidden');
        
        let timeLeft = seconds;
        countdownEl.textContent = timeLeft;
        
        clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
          timeLeft--;
          countdownEl.textContent = timeLeft;
          if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            timerTextEl.classList.add('hidden');
            resendBtn.disabled = false;
          }
        }, 1000);
      }

      window.resendOtpCode = function() {
        const mobile = document.getElementById('mobile').value;
        const email = document.getElementById('email').value;
        sendOtpRequest(mobile, email);
        startOtpCountdown(30);
      };

      let isVerifying = false;

      window.verifyOtpCode = function() {
        if (isVerifying) return;

        const mobile = document.getElementById('mobile').value;
        const email = document.getElementById('email').value;
        const otp = document.getElementById('otpCode').value;
        const errorEl = document.getElementById('otpError');
        const btnVerify = document.getElementById('btnVerifyOtp');
        
        if (!otp || otp.length < 4) {
          errorEl.textContent = "Please enter a valid OTP.";
          errorEl.classList.remove('hidden');
          return;
        }
        
        isVerifying = true;
        errorEl.classList.add('hidden');
        btnVerify.disabled = true;
        btnVerify.innerHTML = `<span>Verifying...</span>`;
        
        const formData = new FormData();
        formData.append('mobile', mobile);
        formData.append('email', email);
        formData.append('otp', otp);
        
        fetch('verify_otp.php', {
          method: 'POST',
          body: formData,
          credentials: 'include'
        })
        .then(response => response.text())
        .then(status => {
          status = status.trim();
          if (status === 'success') {
            isOtpVerified = true;
            btnVerify.innerHTML = `<span>Verified ✓</span>`;
            btnVerify.classList.remove('bg-green-500', 'hover:bg-green-600');
            btnVerify.classList.add('bg-emerald-600', 'text-white');
            
            setTimeout(() => {
              window.closeOtpModal();
              form.submit();
            }, 1000);
          } else if (status === 'expired') {
            isVerifying = false;
            btnVerify.disabled = false;
            btnVerify.innerHTML = `<span>Verify & Register</span>`;
            errorEl.textContent = "OTP has expired. Please click Resend.";
            errorEl.classList.remove('hidden');
          } else {
            isVerifying = false;
            btnVerify.disabled = false;
            btnVerify.innerHTML = `<span>Verify & Register</span>`;
            errorEl.textContent = "Invalid OTP. Please try again.";
            errorEl.classList.remove('hidden');
          }
        })
        .catch(err => {
          isVerifying = false;
          btnVerify.disabled = false;
          btnVerify.innerHTML = `<span>Verify & Register</span>`;
          errorEl.textContent = "Network error. Please try again.";
          errorEl.classList.remove('hidden');
          console.error('Verify error:', err);
        });
      };
    });
  </script>
</body>
</html>
