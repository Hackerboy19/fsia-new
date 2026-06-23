<?php
include("config.php");

$asset_path = 'assets-new';
if (!is_dir($asset_path)) {
    $asset_path = '../assets-new';
}

$token = $_GET['token'] ?? '';
$ires = null;

if (!empty($token)) {
    if ($connect) {
        $token_esc = mysqli_real_escape_string($connect, $token);
        $res = mysqli_query($connect, "SELECT * FROM registration WHERE md5(id) = '$token_esc'");
        if ($res && mysqli_num_rows($res) > 0) {
            $ires = mysqli_fetch_assoc($res);
        }
    }

    // Fallback to Session mock database if database connection is offline or record not found in mysql
    if (!$ires && isset($_SESSION['mock_db'])) {
        foreach ($_SESSION['mock_db'] as $row) {
            if (md5($row['id']) === $token) {
                $ires = $row;
                break;
            }
        }
    }
}

// Redirect back to registration if no token/record and no query parameters name/category found
if (!$ires && empty($_GET['name']) && empty($_GET['category'])) {
    header("Location: miss-universe-beauty-pageant.php");
    exit;
}

$category = "Forever Star India Event";
if ($ires) {
    $category = ($ires['regtype'] == '30') ? "Miss Universe 2026" : "Forever Star India Event";
} elseif (isset($_GET['category'])) {
    $category = $_GET['category'];
}

$name_val = $ires['first_name'] ?? $_GET['name'] ?? 'Applicant';
$phone_val = $ires['mobile'] ?? $_GET['phone'] ?? '';
$email_val = $ires['email'] ?? $_GET['email'] ?? '';
$city_val = $ires['cityfsia'] ?? $ires['city'] ?? $_GET['city'] ?? '';

$wa_msg = "Hello, I have completed the registration. Here are my details:\n\n";
$wa_msg .= "• Name: " . $name_val . "\n";
$wa_msg .= "• Category: " . $category . "\n";
$wa_msg .= "• Phone: " . $phone_val . "\n";
$wa_msg .= "• Email: " . $email_val . "\n";
$wa_msg .= "• City: " . $city_val . "\n\n";
$wa_msg .= "Please assist me with the next step.";
$wa_url = "https://wa.me/919983286999?text=" . urlencode($wa_msg);
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Successful | Forever Star India</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $asset_path; ?>/css/main.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Outfit', 'sans-serif'],
            serif: ['Playfair Display', 'serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen flex flex-col font-sans bg-slate-50">

  <?php include 'header1806.php'; ?>

  <!-- MAIN CONTAINER -->
  <main class="flex-grow flex flex-col items-center justify-center py-12 px-4 bg-slate-50">
    <div class="w-full max-w-2xl z-10">
      
      <!-- Reusable Hero Header Component -->
      <?php 
      include_once 'form_header.php';
      render_form_hero(); 
      ?>

      <!-- SUCCESS NOTICE -->
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-slate-200/60">
        
        <!-- CONFIRMATION BADGE -->
        <div class="flex flex-col items-center text-center mb-8">
          <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mb-4 text-emerald-500 text-3xl font-bold">
            ✓
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/50 mb-3">
            Registration Received!
          </span>
          <h2 class="text-2xl md:text-3xl font-serif font-bold text-slate-900">
            Your application has been submitted successfully.
          </h2>
        </div>

        <!-- CONTEXTUAL DATA FRAME -->
        <div class="bg-slate-50 rounded-2xl border border-slate-200/60 p-6 mb-8">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 font-mono border-b border-slate-200 pb-2">
            Applicant Information Summary
          </h3>
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
            <div>
              <dt class="text-slate-500 font-medium mb-1">Applicant Name</dt>
              <dd class="text-slate-800 font-semibold text-base"><?= htmlspecialchars($name_val) ?></dd>
            </div>
            <div>
              <dt class="text-slate-500 font-medium mb-1">Track Category</dt>
              <dd class="text-amber-600 font-semibold text-base">
                <?= htmlspecialchars($category) ?>
              </dd>
            </div>
            <div class="border-t border-slate-200/60 pt-3 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <dt class="text-slate-500 font-medium mb-1">Contact Profile</dt>
                <dd class="text-slate-700 font-mono"><?= htmlspecialchars($phone_val) ?></dd>
                <dd class="text-slate-500 text-xs mt-0.5 truncate"><?= htmlspecialchars($email_val) ?></dd>
              </div>
              <div>
                <dt class="text-slate-500 font-medium mb-1">Location Profile</dt>
                <dd class="text-slate-700 font-semibold"><?= htmlspecialchars($city_val) ?></dd>
                <dd class="text-slate-500 text-xs mt-0.5"><?= htmlspecialchars($ires['state'] ?? $_GET['state'] ?? '') ?></dd>
              </div>
            </div>
          </dl>
        </div>

        <!-- ACTION ELEMENTS -->
        <div class="space-y-4">
          <!-- Primary CTA Button -->
          <a href="payment-new.php?<?= $ires ? 'token=' . urlencode($token) : http_build_query($_GET) ?>" 
             class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-amber-500/25 transform hover:-translate-y-0.5 transition duration-150 flex items-center justify-center space-x-2 text-lg">
            <span>💳 Step 2 — Complete Payment</span>
          </a>

          <!-- Support Breakout Hook (WhatsApp) -->
          <a href="<?= $wa_url ?>" target="_blank" rel="noopener"
             class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-semibold py-3.5 px-6 rounded-xl transition duration-150 flex items-center justify-center space-x-2 text-sm shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" viewBox="0 0 32 32" fill="currentColor">
              <path d="M16.04 4C9.96 4 5.02 8.94 5.02 15.02c0 1.94.51 3.83 1.47 5.5L4.9 27.2l6.84-1.79c1.61.88 3.43 1.34 5.28 1.34h.01c6.08 0 11.02-4.94 11.02-11.02C28.05 8.94 23.11 4 16.04 4zm0 20.2h-.01c-1.65 0-3.27-.44-4.68-1.28l-.34-.2-3.55.93.95-3.46-.22-.36a9.13 9.13 0 0 1-1.4-4.86c0-5.05 4.11-9.16 9.17-9.16 2.45 0 4.75.96 6.48 2.69a9.1 9.1 0 0 1 2.68 6.48c0 5.05-4.11 9.16-9.16 9.16zm5.03-6.86c-.28-.14-1.63-.8-1.88-.9-.25-.09-.43-.14-.62.14-.18.28-.71.9-.87 1.08-.16.18-.32.2-.6.07-.28-.14-1.16-.43-2.21-1.36-.82-.73-1.37-1.63-1.53-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.32.42-.49.14-.16.18-.28.28-.46.09-.18.05-.35-.02-.49-.07-.14-.62-1.5-.85-2.05-.22-.54-.45-.47-.62-.48l-.53-.01c-.18 0-.48.07-.74.35-.25.28-.96.94-.96 2.3 0 1.36.99 2.67 1.12 2.85.14.18 1.95 2.98 4.73 4.18.66.28 1.18.45 1.58.58.66.21 1.27.18 1.74.11.53-.08 1.63-.67 1.86-1.31.23-.64.23-1.19.16-1.31-.07-.12-.25-.18-.53-.32z"/>
            </svg>
            <span>Share Update on WhatsApp</span>
          </a>

          <!-- Reusable Dynamic Support Module Component -->
          <?php render_emergency_support(); ?>
        </div>

        <!-- URGENCY NOTICE -->
        <div class="mt-8 bg-amber-50 rounded-2xl border border-amber-200/60 p-5 flex items-start space-x-3 text-sm">
          <span class="text-amber-500 text-lg mt-0.5">⚠️</span>
          <div>
            <h4 class="text-amber-600 font-semibold mb-1">Time Sensitive Notice</h4>
            <p class="text-slate-600 leading-relaxed text-xs">
              Complete your payment now to secure your slot for the Miss Universe 2026 city audition. Registrations without completed payments expire after 48 hours.
            </p>
          </div>
        </div>

      </div>

    </div>
  </main>

  <?php include 'footer1806.php'; ?>

  <!-- Navigation/Layout Script -->
  <script src="<?php echo $asset_path; ?>/js/main.js"></script>

</body>
</html>
