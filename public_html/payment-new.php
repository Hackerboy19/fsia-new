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

$error = '';
$pay_name = $ires['first_name'] ?? $_GET['name'] ?? 'Applicant';
$pay_category = $ires ? (($ires['regtype'] == '30') ? 'Miss Universe 2026 Audition' : 'Forever Star India Event') : ($_GET['category'] ?? 'Forever Star India Event');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_payment'])) {
    $txn_id = "TXN" . strtoupper(bin2hex(random_bytes(6)));
    $pdate = date("Y-m-d H:i:s");

    if ($ires) {
        if ($connect) {
            $token_esc = mysqli_real_escape_string($connect, $token);
            $txn_esc = mysqli_real_escape_string($connect, $txn_id);
            mysqli_query($connect, "UPDATE registration SET payment_status = 'completed', trans_id = '$txn_esc', pdate = '$pdate' WHERE md5(id) = '$token_esc'");
        } else {
            // Update session mock
            foreach ($_SESSION['mock_db'] as $key => $row) {
                if (md5($row['id']) === $token) {
                    $_SESSION['mock_db'][$key]['payment_status'] = 'completed';
                    $_SESSION['mock_db'][$key]['trans_id'] = $txn_id;
                    $_SESSION['mock_db'][$key]['pdate'] = $pdate;
                    break;
                }
            }
        }
        header("Location: payment-success.php?token=" . urlencode($token));
        exit;
    } else {
        // Redirect for client-side forms, passing details as query parameters
        $params = $_GET;
        $params['txn_id'] = $txn_id;
        $params['pdate'] = $pdate;
        $params['amount'] = '₹1,999';
        header("Location: payment-success.php?" . http_build_query($params));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HDFC Secure Checkout | Forever Star India</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $asset_path; ?>/css/main.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Outfit', 'sans-serif'],
            poppins: ['Poppins', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen flex flex-col font-sans bg-slate-50 text-slate-800">

  <?php include 'header1806.php'; ?>

  <!-- CHECKOUT SECTION -->
  <main class="flex-grow flex flex-col items-center justify-center py-12 px-4 bg-slate-50">
    
    <!-- Reusable Hero Header Component -->
    <?php 
    include_once 'form_header.php';
    render_form_hero(); 
    ?>
    
    <div class="w-full max-w-4xl z-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- INVOICE SUMMARY PANEL -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-xl">
          <div class="flex items-center space-x-2 text-xs font-bold tracking-widest text-slate-400 uppercase mb-4 font-mono">
            <span>Invoice Summary</span>
          </div>
          
          <div class="space-y-4 text-sm">
            <div>
              <span class="text-slate-550 block text-xs">Applicant Name</span>
              <span class="text-slate-850 font-semibold text-base"><?= htmlspecialchars($pay_name) ?></span>
            </div>
            <div>
              <span class="text-slate-550 block text-xs">Category</span>
              <span class="text-slate-700 font-medium"><?= htmlspecialchars($pay_category) ?></span>
            </div>
            <hr class="border-slate-100">
            <div class="flex justify-between items-center text-lg font-bold pt-2">
              <span class="text-slate-800 font-serif">Subtotal</span>
              <span class="text-amber-600 font-poppins">₹2,999</span>
            </div>
          </div>
        </div>

        <div class="bg-blue-50/50 border border-blue-100 rounded-3xl p-5 text-xs text-slate-500 space-y-2">
          <div class="flex items-center space-x-2 text-blue-600 font-semibold">
            <span>🛡️ 256-bit SSL Encryption</span>
          </div>
          <p class="leading-relaxed">
            Your transaction is securely routed using bank-grade 3-D Secure HDFC Gateway authentication protocols.
          </p>
        </div>
      </div>

      <!-- HDFC GATEWAY GATE -->
      <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-200/60">
          
          <!-- HDFC HEADER -->
          <div class="bg-gradient-to-r from-blue-800 to-indigo-900 px-8 py-5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="bg-white rounded px-2 py-1 flex items-center justify-center font-bold text-xs text-blue-900 tracking-tighter shadow-sm">
                HDFC BANK
              </div>
              <span class="text-white text-xs font-semibold uppercase tracking-wider font-mono">Secure Payment Gateway</span>
            </div>
            <div class="flex items-center space-x-1.5 text-xs text-blue-150">
              <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
              <span class="text-emerald-300">Online</span>
            </div>
          </div>

          <!-- TABS NAVIGATION -->
          <div class="border-b border-slate-100 flex text-center text-sm font-semibold select-none cursor-pointer">
            <div id="tab-upi" onclick="switchTab('upi')" class="flex-1 py-4 border-b-2 border-amber-500 text-amber-600 transition">
              📱 UPI / QR Code
            </div>
            <div id="tab-card" onclick="switchTab('card')" class="flex-1 py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition">
              💳 Card Details
            </div>
            <div id="tab-net" onclick="switchTab('net')" class="flex-1 py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition">
              🏦 Net Banking
            </div>
          </div>

          <!-- INTERACTIVE PANELS -->
          <div class="p-8">
            <form action="" method="POST" id="mainPaymentForm">
              <input type="hidden" name="complete_payment" value="1">

              <!-- UPI & QR Code Generator -->
              <div id="panel-upi" class="space-y-6">
                <div class="text-center p-6 bg-slate-50 rounded-2xl border border-slate-200/60 flex flex-col items-center">
                  <p class="text-xs text-slate-500 mb-4 font-mono">Scan the secure HDFC dynamic QR code using any UPI app</p>
                  
                  <div class="relative w-44 h-44 bg-white p-3 rounded-2xl shadow-inner flex items-center justify-center border border-slate-200">
                    <div id="qrLoader" class="absolute inset-0 bg-white/95 rounded-2xl flex flex-col items-center justify-center transition-opacity duration-300">
                      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                      <span class="text-xs text-slate-500 mt-2 font-mono">Generating QR...</span>
                    </div>
                    <!-- Styled Simulated QR Code -->
                    <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M2 2h6v6H2V2zm2 2v2h2V4H4zm1 1h2v2H5V5zm9-3h6v6h-6V2zm2 2v2h2V4h-2zm1 1h2v2h-2V5zM2 14h6v6H2v-6zm2 2v2h2v-2H4zm1 1h2v2H5v-2zm9 2h2v2h-2v-2zm4-6h2v4h-2v-4zm-4 2h2v2h-2v-2zm2 2h2v2h-2v-2zm2-4h2v2h-2v-2zm-6-2h2v2h-2v-2zm2 2h2v2h-2v-2zm2-2h2v2h-2v-2zm-6-4h2v2h-2V6zm8 4h2v2h-2v-2z"/>
                    </svg>
                  </div>
                  
                  <span class="text-xs text-amber-600 font-semibold tracking-wider uppercase mt-4">✓ Verified Merchant: Forever Star India</span>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold py-4 px-6 rounded-xl shadow-lg transition text-base">
                  Simulate QR Payment Complete
                </button>
              </div>

              <!-- Credit/Debit Card Form -->
              <div id="panel-card" class="space-y-6 hidden">
                <div class="space-y-4">
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Cardholder Name</label>
                    <input type="text" placeholder="John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition">
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Card Number</label>
                    <div class="relative">
                      <input type="text" placeholder="4111 2222 3333 4444" maxlength="19" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition font-mono">
                      <span class="absolute inset-y-0 right-4 flex items-center text-slate-400 font-bold text-xs">VISA / MC</span>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Expiry Date</label>
                      <input type="text" placeholder="MM/YY" maxlength="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition font-mono">
                    </div>
                    <div>
                      <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">CVV</label>
                      <input type="password" placeholder="•••" maxlength="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-amber-500 outline-none transition font-mono">
                    </div>
                  </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold py-4 px-6 rounded-xl shadow-lg transition text-base">
                  Pay Securely - ₹2,999
                </button>
              </div>

              <!-- Net Banking portal selectors -->
              <div id="panel-net" class="space-y-6 hidden">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500" checked>
                    <span class="text-sm font-semibold text-slate-700">HDFC Bank</span>
                  </label>
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-700">ICICI Bank</span>
                  </label>
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-700">SBI</span>
                  </label>
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-700">Axis Bank</span>
                  </label>
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-700">Kotak Bank</span>
                  </label>
                  <label class="flex items-center space-x-3 p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500/50 transition">
                    <input type="radio" name="bank" class="text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-700">Yes Bank</span>
                  </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold py-4 px-6 rounded-xl shadow-lg transition text-base">
                  Proceed to Bank Portal
                </button>
              </div>

            </form>
          </div>

          <!-- ASSISTANCE COMPONENT (Accordion fallback) -->
          <div class="border-t border-slate-100 p-6 bg-slate-50/50">
            <button type="button" onclick="toggleAccordion()" class="w-full flex items-center justify-between text-xs font-bold text-slate-500 hover:text-slate-700 uppercase tracking-widest transition outline-none font-mono text-left">
              <span>If you have any queries regarding your payment or require assistance, click here to connect with our managers via WhatsApp.</span>
              <span id="accordion-arrow" class="text-lg transition-transform duration-200">+</span>
            </button>
            
            <div id="accordion-content" class="mt-4 space-y-3 hidden transition-all duration-200">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <a href="https://wa.me/919983286999?text=Hi! I need help with my Miss Universe 2026 payment." target="_blank"
                   class="flex items-center justify-between p-3.5 bg-white border border-slate-200 rounded-xl hover:border-emerald-500/30 shadow-sm transition text-sm">
                  <div class="flex items-center space-x-3">
                    <span class="text-emerald-500">💬</span>
                    <div>
                      <p class="font-semibold text-slate-800">Finance Manager</p>
                      <p class="text-xs text-slate-500">+91-99832-86999</p>
                    </div>
                  </div>
                  <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded border border-emerald-200/50">Active</span>
                </a>

                <a href="https://wa.me/919983599666?text=Hi! I have query regarding payment transaction." target="_blank"
                   class="flex items-center justify-between p-3.5 bg-white border border-slate-200 rounded-xl hover:border-emerald-500/30 shadow-sm transition text-sm">
                  <div class="flex items-center space-x-3">
                    <span class="text-emerald-500">💬</span>
                    <div>
                      <p class="font-semibold text-slate-800">Support Desk</p>
                      <p class="text-xs text-slate-500">+91-99835-99666</p>
                    </div>
                  </div>
                  <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded border border-emerald-200/50">Active</span>
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </main>

  <?php include 'footer1806.php'; ?>

  <!-- Tabs Navigation & Accordion Logic -->
  <script>
    function switchTab(tabId) {
      // Hide all panels
      document.getElementById('panel-upi').classList.add('hidden');
      document.getElementById('panel-card').classList.add('hidden');
      document.getElementById('panel-net').classList.add('hidden');
      
      // Remove active tab styling
      document.getElementById('tab-upi').className = "flex-1 py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition";
      document.getElementById('tab-card').className = "flex-1 py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition";
      document.getElementById('tab-net').className = "flex-1 py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition";

      // Show requested panel and set active styling
      if (tabId === 'upi') {
        document.getElementById('panel-upi').classList.remove('hidden');
        document.getElementById('tab-upi').className = "flex-1 py-4 border-b-2 border-amber-500 text-amber-600 font-semibold transition";
        // Reset loader simulation
        const loader = document.getElementById('qrLoader');
        loader.style.opacity = '1';
        loader.style.pointerEvents = 'auto';
        setTimeout(() => {
          loader.style.opacity = '0';
          loader.style.pointerEvents = 'none';
        }, 1200);
      } else if (tabId === 'card') {
        document.getElementById('panel-card').classList.remove('hidden');
        document.getElementById('tab-card').className = "flex-1 py-4 border-b-2 border-amber-500 text-amber-600 font-semibold transition";
      } else if (tabId === 'net') {
        document.getElementById('panel-net').classList.remove('hidden');
        document.getElementById('tab-net').className = "flex-1 py-4 border-b-2 border-amber-500 text-amber-600 font-semibold transition";
      }
    }

    function toggleAccordion() {
      const content = document.getElementById('accordion-content');
      const arrow = document.getElementById('accordion-arrow');
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        arrow.textContent = "−";
      } else {
        content.classList.add('hidden');
        arrow.textContent = "+";
      }
    }

    // Trigger QR generator simulation on load
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        const loader = document.getElementById('qrLoader');
        if (loader) {
          loader.style.opacity = '0';
          loader.style.pointerEvents = 'none';
        }
      }, 1200);
    });
  </script>

  <!-- Navigation/Layout Script -->
  <script src="<?php echo $asset_path; ?>/js/main.js"></script>
</body>
</html>
