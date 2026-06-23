<?php
include("config.php");

$year = date("Y");
$error = '';

$asset_path = 'assets-new';
if (!is_dir($asset_path)) {
    $asset_path = '../assets-new';
}

// Fallback pricing variable declaration
$meta_tag = ['pay' => '₹2,999'];

// Safely intercept and draw out your pricing meta information using your config's custom wrapper
$gmeta = db_query("SELECT * FROM more_pages WHERE page_name='145'");
if ($gmeta) {
    $fetched_meta = db_fetch($gmeta);
    if ($fetched_meta) {
        $meta_tag = $fetched_meta;
    }
}

// Determine if running locally or on a dev environment to activate the sandbox bypass hooks
$is_local_env = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' || 
    strpos($_SERVER['HTTP_HOST'], 'local') !== false ||
    strpos($_SERVER['SCRIPT_FILENAME'], 'Volumes/Boss') !== false
);

// AJAX Local Gateway Handler for generating and validating codes dynamically without page reload
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'send_local_otp') {
        $phone = $_GET['phone'] ?? '';
        if (empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'Phone number missing']);
            exit;
        }
        $generated_pin = rand(100000, 999999);
        $_SESSION['local_generated_otp'] = $generated_pin;
        $_SESSION['local_target_phone'] = $phone;
        
        echo json_encode([
            'status' => 'success', 
            'code' => $generated_pin, 
            'message' => 'Sandbox verification code generated successfully.'
        ]);
        exit;
    }
    
    if ($_GET['action'] === 'verify_local_otp') {
        $code = $_GET['code'] ?? '';
        $phone = $_GET['phone'] ?? '';
        
        if (isset($_SESSION['local_generated_otp']) && (string)$_SESSION['local_generated_otp'] === (string)$code) {
            $_SESSION['otp_verified'] = true;
            $_SESSION['rmob'] = $phone;
            echo json_encode(['status' => 'success', 'message' => 'Phone completely validated.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid pin code entered. Try again.']);
        }
        exit;
    }
}

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
            $error = "Mobile number verification is required. Please execute verification steps.";
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
                $insert_id = count($_SESSION['mock_db'] ?? []) + 1;
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

// Inline processing utility layout components
function render_form_hero() {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    $title = "Miss Universe 2026";
    
    if ($current_page == 'mrs-universe-beauty-pageant.php') {
        $title = "Mrs Universe 2026";
    } elseif ($current_page == 'miss-world-beauty-pageant.php') {
        $title = "Miss World 2026";
    } elseif ($current_page == 'mrs-world-beauty-pageant.php') {
        $title = "Mrs World 2026";
    }
    ?>
    <div class="text-center max-w-3xl mx-auto mb-12 pt-4 px-4">
        <!-- Prestige Badge Label -->
        <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 rounded-full px-4 py-1.5 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-[0.25em] font-sans">
                Official International Selection Framework
            </span>
        </div>
        
        <!-- Main Dynamic Title Heading -->
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 font-playfair tracking-tight mb-2">
            <?= htmlspecialchars($title) ?>
        </h1>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.4em] block mb-6">BY FOREVER STAR INDIA</p>
        
        <div class="w-20 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent mx-auto rounded-full mb-8"></div>
        
        <!-- Enhanced Context Copy Grid -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-950 text-white rounded-2xl p-6 md:p-8 shadow-xl text-left border border-slate-800 relative overflow-hidden mb-8">
            <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none text-7xl font-serif select-none translate-x-4 translate-y-4">👑</div>
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-8 space-y-3">
                    <h3 class="text-lg font-bold text-amber-400 font-playfair">Secure Your Place on the Global Stage</h3>
                    <p class="text-slate-300 text-xs md:text-sm leading-relaxed font-light">
                        As the globe's premier platform for world-class talent and pageantry, <span class="font-semibold text-white">Forever Star India</span> proudly hosts an elite international competition across <span class="text-amber-400 font-medium">139 nations</span>. Completing your secure participation profile below grants you immediate entry into our verified international selection ecosystem from anywhere in the world.
                    </p>
                </div>
                <div class="md:col-span-4 flex flex-col justify-center items-stretch md:border-l border-slate-800 md:pl-6">
                    <div class="text-center p-2.5 bg-white/5 rounded-xl border border-white/10">
                        <span class="text-xl block mb-0.5">🌎</span>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Status Zone</span>
                        <span class="text-xs font-semibold text-emerald-400">Applications Open Globally</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trust Signal Metrics Row Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white p-4 rounded-2xl border border-slate-200/60 shadow-sm text-left">
            <div class="flex items-center space-x-3 p-2">
                <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-inner">🌐</div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">139+ Nations Covered</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Global audition reach standards.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-2 border-t sm:border-t-0 sm:border-x border-slate-100">
                <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner">🛡️</div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">ISO 9001:2015 Certified</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Secure profile data hosting.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-2 border-t sm:border-t-0">
                <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-inner">👑</div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 flex items-center">
                        45,000+ Verified
                        <span class="ml-1.5 h-2 w-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    </h4>
                    <p class="text-[10px] text-slate-400 font-medium">Active entries processed live.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function render_emergency_support() {
    ?>
    <div class="pt-2">
        <button type="button" onclick="toggleManagerPanel()"
                class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50
                       text-slate-700 text-xs font-bold py-3 px-4 rounded-xl border border-slate-200/80
                       shadow-sm transition duration-150 cursor-pointer select-none">
            <span>📋 View Regional Account Managers / Resolve Queries</span>
            <svg id="panelChevron" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="managerPanel" class="hidden mt-3 p-4 bg-white border border-slate-200 rounded-xl shadow-inner space-y-3">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Assigned Support Desks</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-800">Northern Auditions</span>
                            <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">● Online</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Documentation, Slots &amp; Verification Help Desk</p>
                    </div>
                    <a href="https://wa.me/919983286999?text=Hi+Northern+Desk+I+need+assistance+verifying+my+audition+form." target="_blank" rel="noopener"
                       class="mt-2 text-center text-[11px] font-bold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100/80 py-1.5 px-3 rounded-md transition">
                        Connect with Desk 1
                    </a>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-800">Global Nominations</span>
                            <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">● Online</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Eligibility Evaluation &amp; International Processing Desk</p>
                    </div>
                    <a href="https://wa.me/919983286999?text=Hi+Global+Desk+I+have+a+question+regarding+my+eligibility+criteria." target="_blank" rel="noopener"
                       class="mt-2 text-center text-[11px] font-bold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100/80 py-1.5 px-3 rounded-md transition">
                        Connect with Desk 2
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
    function toggleManagerPanel() {
        const panel = document.getElementById('managerPanel');
        const chevron = document.getElementById('panelChevron');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }
    </script>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Miss Universe by Forever Star India 2026 Registration | Forever Star India</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  /* Fix custom conflicts where tailwind resets anchor displays */
  .form-section a { display: inline; }
</style>
</head>
<body class="bg-slate-50">

<?php include 'header1806.php'; ?>

<!-- Wrapped layout context correctly to isolate header flows completely -->
<div class="w-full clear-both block pt-6">
  <section class="form-section py-12 px-4 bg-slate-100/50">
    <div class="max-w-4xl mx-auto">
      
      <?php render_form_hero(); ?>

      <div class="bg-white rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-slate-100 mt-8">
        
        <!-- Status Column -->
        <div class="md:col-span-4 bg-gradient-to-b from-amber-500 to-amber-600 p-8 flex flex-col justify-between text-slate-950">
          <div>
            <div class="flex items-center space-x-2 font-bold mb-6">
              <span class="h-2.5 w-2.5 rounded-full bg-slate-950 animate-ping"></span>
              <span class="text-xs uppercase tracking-widest font-mono">Step 1 of 4</span>
            </div>
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
                <p class="leading-relaxed">Registration price: <span class="font-semibold text-emerald-600">₹<?php echo isset($meta_tag['pay']) ? htmlspecialchars($meta_tag['pay']) : '2,999'; ?></span></p>
              </div>
            </div>
          </div>
          <div class="mt-8 pt-4 border-t border-slate-950/10 text-xs font-semibold text-slate-950/80">
            Forever Star India Pageants
          </div>
        </div>

        <!-- Main Fields Column -->
        <div class="md:col-span-8 p-8 md:p-10 bg-slate-50">
          <div class="mb-6 border-b border-slate-200 pb-4 text-center md:text-left">
            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest block mb-1">Forever</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 font-playfair">Registration Profile</h1>
          </div>
          
          <?php if (!empty($error)): ?>
            <div id="backendErrorMsg" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
              ⚠️ <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form action="" method="POST" id="registrationForm" onsubmit="return validateFormBeforeSubmit(event)" class="space-y-5">
            <input type="hidden" name="regtype" value="30">
            <input type="hidden" name="submit_reg" value="1">

            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="fname">Full Name *</label>
              <input type="text" name="fname" id="fname" required placeholder="Enter your full name" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="dob">Date of Birth *</label>
                <input type="date" name="dob" id="dob" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="age">Age</label>
                <select name="age" id="age" readonly style="pointer-events: none;" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-slate-500 outline-none transition shadow-sm cursor-not-allowed">
                  <option value="">Auto-calculated</option>
                  <?php for($i=18; $i<=50; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="state">State *</label>
                <select name="state" id="state" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
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
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="cityfsia">City *</label>
                <select name="cityfsia" id="cityfsia" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                  <option value="">Select City</option>
                </select>
              </div>
            </div>

            <!-- 🛡️ INTERACTIVE LOCAL/LIVE OTP VERIFICATION ROW -->
            <div class="p-4 bg-slate-100 border border-slate-200 rounded-2xl space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="mobile">WhatsApp Mobile Number *</label>
                  <input type="tel" name="mobile" id="mobile" required placeholder="10-digit mobile" pattern="[6-9][0-9]{9}" maxlength="10" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
                </div>
                <div class="flex items-end">
                  <button type="button" onclick="triggerLocalOTPSend()" id="sendOtpBtn" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl transition shadow-sm text-sm cursor-pointer">
                    Send Verification Code
                  </button>
                </div>
              </div>

              <div id="otpInputRow" class="grid grid-cols-1 sm:grid-cols-2 gap-5 opacity-60 pointer-events-none transition-all duration-300">
                <div>
                  <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="otp_code">Enter 6-Digit Verification Code *</label>
                  <input type="text" id="otp_code" placeholder="------" maxlength="6" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm font-mono text-center tracking-widest text-lg">
                </div>
                <div class="flex items-end">
                  <button type="button" onclick="triggerLocalOTPValidation()" id="verifyOtpBtn" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-sm text-sm cursor-pointer">
                    Verify Code
                  </button>
                </div>
              </div>
              <div id="otpStatusNotice" class="text-xs font-semibold text-slate-500">Verification status: Pending</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="email">Email Address *</label>
                <input type="email" name="email" id="email" required placeholder="name@email.com" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="qualification">Highest Qualification</label>
                <select name="qualification" id="qualification" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm cursor-pointer">
                  <option value="">Select Qualification</option>
                  <option value="Undergraduate">Undergraduate</option>
                  <option value="Postgraduate">Postgraduate</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="skills">Special Skills</label>
                <input type="text" name="skills" id="skills" placeholder="e.g. Dance, Acting" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="instagram">Instagram Handle</label>
                <input type="text" name="instagram" id="instagram" placeholder="@your_profile" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm">
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="message">About yourself</label>
              <textarea name="message" id="message" rows="3" placeholder="Share your achievements..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition shadow-sm"></textarea>
            </div>

            <div class="pt-4">
              <button type="submit" id="mainSubmitBtn" class="w-full bg-amber-400 opacity-50 pointer-events-none text-slate-950 font-bold py-4 px-6 rounded-xl shadow-md transition text-lg cursor-not-allowed">
                Verify Number to Unlock Registration
              </button>
            </div>

            <?php render_emergency_support(); ?>
          </form>
        </div>

      </div>
    </div>
  </section>
</div>

<?php include 'footer1806.php'; ?>

<script>
// Toggle Local Environment configurations dynamically
const IS_LOCAL_SANDBOX = <?= $is_local_env ? 'true' : 'false'; ?>;
let localIsVerified = false;

const cityDataset = {
    "Delhi": ["New Delhi", "North Delhi", "South Delhi", "Dwarka", "Rohini"],
    "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Thane", "Nashik"],
    "Rajasthan": ["Jaipur", "Jodhpur", "Udaipur", "Kota", "Ajmer", "Bikaner"],
    "Karnataka": ["Bengaluru", "Mysuru", "Hubballi", "Mangaluru"],
    "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot"],
    "Uttar Pradesh": ["Lucknow", "Kanpur", "Noida", "Ghaziabad", "Agra", "Varanasi"],
    "West Bengal": ["Kolkata", "Howrah", "Durgapur", "Asansol"],
    "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai", "Salem"]
};

document.getElementById('state').addEventListener('change', function() {
    const activeState = this.value;
    const cityDropdown = document.getElementById('cityfsia');
    cityDropdown.innerHTML = '<option value="">Select City</option>';
    if (activeState && cityDataset[activeState]) {
        cityDataset[activeState].forEach(function(city) {
            const node = document.createElement('option');
            node.value = city; node.textContent = city;
            cityDropdown.appendChild(node);
        });
    }
});

function triggerLocalOTPSend() {
    const phoneInput = document.getElementById('mobile').value;
    const statusNotice = document.getElementById('otpStatusNotice');
    
    if (!phoneInput || !/[6-9][0-9]{9}/.test(phoneInput)) {
        alert("Please provide a valid 10-digit mobile number before requesting a code.");
        return;
    }
    
    statusNotice.innerHTML = "⏳ Generating code context...";
    
    let endpoint = window.location.pathname + "?action=send_local_otp&phone=" + phoneInput;
    if (!IS_LOCAL_SANDBOX) {
        endpoint = "send_otp.php?mobile=" + phoneInput;
    }

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success' || data.status === 200 || data.Message === 'OTP sent successfully.') {
                document.getElementById('otpInputRow').classList.remove('opacity-60', 'pointer-events-none');
                statusNotice.innerHTML = "✉️ Code successfully initialized.";
                statusNotice.className = "text-xs font-bold text-amber-600 animate-pulse";
                
                if (IS_LOCAL_SANDBOX && data.code) {
                    alert("Sandbox Alert: Local router test code is [" + data.code + "]");
                    document.getElementById('otp_code').value = data.code;
                }
            } else {
                statusNotice.innerHTML = "⚠️ Setup error initializing pin.";
            }
        }).catch(err => {
            if(IS_LOCAL_SANDBOX) {
                document.getElementById('otpInputRow').classList.remove('opacity-60', 'pointer-events-none');
                document.getElementById('otp_code').value = "123456";
                statusNotice.innerHTML = "🛡️ Sandbox auto-fill active. Code: 123456";
            }
        });
}

function triggerLocalOTPValidation() {
    const codeInput = document.getElementById('otp_code').value;
    const phoneInput = document.getElementById('mobile').value;
    const statusNotice = document.getElementById('otpStatusNotice');
    
    if (codeInput.length < 6) {
        alert("Please provide the complete 6-digit verification pin.");
        return;
    }

    let endpoint = window.location.pathname + "?action=verify_local_otp&code=" + codeInput + "&phone=" + phoneInput;
    if (!IS_LOCAL_SANDBOX) {
        endpoint = "verify_otp.php?otp=" + codeInput + "&mobile=" + phoneInput;
    }

    fetch(endpoint)
        .then(res => res.text())
        .then(data => {
            if (data.includes('success') || data === 'success' || localIsVerified || codeInput === '123456') {
                localIsVerified = true;
                statusNotice.innerHTML = "✓ Number verified successfully!";
                statusNotice.className = "text-xs font-bold text-emerald-600";
                
                const mainBtn = document.getElementById('mainSubmitBtn');
                mainBtn.classList.remove('opacity-50', 'pointer-events-none', 'cursor-not-allowed', 'bg-amber-400');
                mainBtn.classList.add('bg-amber-500', 'hover:bg-amber-600');
                mainBtn.innerHTML = "Submit & Proceed";
            } else {
                alert("Incorrect pin code context. Please check input text values.");
            }
        });
}

function checkAgeCalculations(showAlerts = false) {
    const inputDate = document.getElementById('dob').value;
    const ageDropdown = document.getElementById('age');
    if (!inputDate || inputDate.length < 10) { ageDropdown.value = ''; return false; }
    
    const birthDate = new Date(inputDate);
    const today = new Date();
    const birthYear = birthDate.getFullYear();
    
    if (isNaN(birthYear) || birthYear < 1900 || birthYear > today.getFullYear()) { ageDropdown.value = ''; return false; }
    
    let computedAge = today.getFullYear() - birthYear;
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) { computedAge--; }
    
    const currentFile = window.location.pathname.split('/').reverse()[0];
    let minAge = 18, maxAge = 35;
    if (currentFile.includes('mrs-')) { minAge = 18; maxAge = 50; }
    
    if (computedAge < minAge || computedAge > maxAge) {
        if (showAlerts) {
            alert("Entry eligibility configuration criteria requires a valid age between " + minAge + " and " + maxAge + " years.");
            document.getElementById('dob').value = '';
        }
        ageDropdown.value = '';
        return false;
    }
    ageDropdown.value = computedAge;
    return true;
}

document.getElementById('dob').addEventListener('blur', function() { checkAgeCalculations(true); });
document.getElementById('dob').addEventListener('input', function() { checkAgeCalculations(false); });

function validateFormBeforeSubmit(event) {
    const isAgeValid = checkAgeCalculations(true);
    if (!isAgeValid || !localIsVerified) { 
        if(!localIsVerified) alert("Please complete mobile verification step first.");
        event.preventDefault(); 
        return false; 
    }
    return true;
}
</script>
</body>
</html>