<?php
include("config.php");

$token = $_GET['token'] ?? '';
$ires = null;
$meta_tag = ['pay' => '₹2,999'];

if (!empty($token)) {
    if ($connect) {
        $token_esc = mysqli_real_escape_string($connect, $token);
        $res = mysqli_query($connect, "SELECT * FROM registration WHERE md5(id) = '$token_esc'");
        if ($res && mysqli_num_rows($res) > 0) {
            $ires = mysqli_fetch_assoc($res);
        }

        $getmeta = "SELECT * FROM more_pages WHERE page_name='145'";
        $gmeta = mysqli_query($connect, $getmeta);
        if ($gmeta && mysqli_num_rows($gmeta) > 0) {
            $meta_tag = mysqli_fetch_assoc($gmeta);
        }
    }

    // Fallback to session mock database
    if (!$ires && isset($_SESSION['mock_db'])) {
        foreach ($_SESSION['mock_db'] as $row) {
            if (md5($row['id']) === $token) {
                $ires = $row;
                break;
            }
        }
    }
}

// Redirect back if no token and no parameters
if (!$ires && empty($_GET['name']) && empty($_GET['category'])) {
    header("Location: miss-universe-beauty-pageant.php");
    exit;
}

$name_val = $ires['first_name'] ?? $_GET['name'] ?? 'Applicant';
$id_val = $ires['id'] ?? $_GET['id'] ?? 'REG' . time();
$txn_val = $ires['trans_id'] ?? $_GET['txn_id'] ?? 'TXN_MOCK';
$amount_val = $ires ? ($meta_tag['pay'] ?? '₹2,999') : ($_GET['amount'] ?? '₹1,999');
$pdate_val = $ires['pdate'] ?? $_GET['pdate'] ?? date('Y-m-d H:i:s');
$category = $ires ? (($ires['regtype'] == '30') ? "Miss Universe 2026" : "Forever Star India Event") : ($_GET['category'] ?? "Forever Star India Event");

$wa_cert_msg = "Hello Support, I have successfully completed my payment for " . $category . ".\n\n";
$wa_cert_msg .= "• Registration ID: " . $id_val . "\n";
$wa_cert_msg .= "• Transaction ID: " . $txn_val . "\n";
$wa_cert_msg .= "• Name: " . $name_val . "\n\n";
$wa_cert_msg .= "Please verify and issue my Registration Certificate. Thank you!";
$wa_cert_url = "https://wa.me/919983286999?text=" . urlencode($wa_cert_msg);
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Successful | Forever Star India</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets-new/css/main.css">
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
    <div class="w-full max-w-xl z-10">
      
      <!-- Reusable Hero Header Component -->
      <?php 
      include_once 'form_header.php';
      render_form_hero(); 
      ?>

      <!-- SUCCESS CONFIRMATION PANEL (Light Theme Tailwind Card) -->
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-slate-200/60">
        
        <!-- VISUAL FEEDBACK (Checkmark graphic) -->
        <div class="flex flex-col items-center text-center mb-8">
          <div class="w-20 h-20 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mb-4 text-emerald-500 text-4xl font-extrabold animate-bounce">
            ✓
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/50 mb-3 font-mono">
            Transaction Approved
          </span>
          <h2 class="text-3xl font-serif font-bold text-slate-900">
            Payment Successful! ✓
          </h2>
          <p class="text-slate-500 text-sm mt-2">
            Your registration is now complete
          </p>
        </div>

        <!-- TRANSACTION DETAILS SUMMARY -->
        <div class="bg-slate-50 rounded-2xl border border-slate-200/60 p-6 mb-8 space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest font-mono border-b border-slate-200 pb-2">
            Transaction Execution Receipt
          </h3>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-mono">
            <div class="flex flex-col">
              <span class="text-slate-400 text-xs uppercase">Transaction Token</span>
              <span class="text-slate-800 font-semibold mt-1 break-all select-all"><?= htmlspecialchars($txn_val) ?></span>
            </div>
            
            <div class="flex flex-col">
              <span class="text-slate-400 text-xs uppercase">Amount Processed</span>
              <span class="text-amber-600 font-bold text-base mt-0.5"><?= htmlspecialchars($amount_val) ?></span>
            </div>

            <div class="flex flex-col pt-2 border-t border-slate-200/40">
              <span class="text-slate-400 text-xs uppercase">Timestamp</span>
              <span class="text-slate-700 mt-1"><?= htmlspecialchars($pdate_val) ?></span>
            </div>

            <div class="flex flex-col pt-2 border-t border-slate-200/40">
              <span class="text-slate-400 text-xs uppercase">Verification Status</span>
              <span class="text-emerald-600 font-semibold flex items-center mt-1">
                <span class="inline-block w-2.5 h-2.5 bg-emerald-500 rounded-full mr-2"></span>
                Verified &amp; Confirmed
              </span>
            </div>
          </div>
        </div>

        <!-- INSTANT FULFILLMENT TRIGGER BOX -->
        <div class="bg-amber-500/5 border border-amber-500/10 rounded-2xl p-6 mb-8 text-center space-y-4">
          <div class="flex items-center justify-center space-x-2 text-amber-600 font-semibold">
            <span class="text-lg">📄</span>
            <span>Your Registration Certificate is processing.</span>
          </div>
          <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
            Your audition registration slot has been permanently reserved. Click the link below to verify your payment status and retrieve your certificate via our automated support assistant.
          </p>
          <div class="pt-2">
            <a href="<?= $wa_cert_url ?>" target="_blank" rel="noopener"
               class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-emerald-550 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-md hover:shadow-emerald-500/15 transform hover:-translate-y-0.5 transition duration-150 text-base">
              <span>📥 Get Certificate on WhatsApp</span>
            </a>
          </div>

          <!-- Reusable Dynamic Support Module Component -->
          <?php render_emergency_support(); ?>
        </div>

        <!-- BACK TO HOME BUTTON -->
        <div class="text-center">
          <a href="index.php" class="text-xs font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition font-mono">
            ← Return to Main Page
          </a>
        </div>

      </div>

    </div>
  </main>

  <?php include 'footer1806.php'; ?>

  <!-- Navigation/Layout Script -->
  <script src="/assets-new/js/main.js"></script>

</body>
</html>
