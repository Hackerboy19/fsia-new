<?php
/**
 * send_otp.php — FSIA OTP Generation & Delivery
 * Generates a 6-digit OTP, stores it in session, and sends via:
 *   1. ClickChat WhatsApp webhook (primary)
 *   2. PHPMailer Gmail SMTP (if installed)
 *   3. PHP native mail() fallback
 */

// ─── HEADERS ────────────────────────────────────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');

// Handle pre-flight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ─── ERROR SUPPRESSION ───────────────────────────────────────────────────────
ini_set('display_errors', 0);
error_reporting(0);

// ─── SESSION ─────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── CONFIG ──────────────────────────────────────────────────────────────────
include('config.php');

// ─── ONLY ACCEPT POST ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// ─── VALIDATE MOBILE ─────────────────────────────────────────────────────────
$mobile = preg_replace('/\D/', '', $_POST['mobile'] ?? '');
$email  = trim($_POST['email'] ?? '');

if (empty($mobile)) {
    echo json_encode(['success' => false, 'message' => 'Mobile number is required.']);
    exit;
}

// ─── GENERATE OTP ────────────────────────────────────────────────────────────
$otp = rand(100000, 999999);

// Store OTP in session
$_SESSION['otp']        = $otp;
$_SESSION['mobile']     = $mobile;
$_SESSION['email']      = $email;
$_SESSION['otp_time']   = time();

// ─── BUILD WHATSAPP NUMBER ────────────────────────────────────────────────────
$clickchat_mobile = $mobile;
if (strlen($mobile) === 10 && in_array($mobile[0], ['6', '7', '8', '9'])) {
    $clickchat_mobile = '91' . $mobile;
}

// ─── 1. WHATSAPP VIA CLICKCHAT ────────────────────────────────────────────────
$whatsapp_sent = false;
if (php_sapi_name() !== 'cli') {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => "https://auto.clickchat.io/webhook/682db5d3169b2264d88b3af5?number=" . $clickchat_mobile . "&message=otp," . $otp,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "GET",
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            "cache-control: no-cache",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        ],
    ]);
    $wa_response = curl_exec($ch);
    $wa_error    = curl_error($ch);
    $wa_http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $whatsapp_sent = (!$wa_error && $wa_http >= 200 && $wa_http < 300);
}

// ─── 2. EMAIL VIA PHPMAILER (if installed) ────────────────────────────────────
$email_sent = false;
if (!empty($email) && php_sapi_name() !== 'cli') {

    $phpmailer_base = __DIR__ . '/PHPMailer/src';
    $has_phpmailer  = (
        file_exists($phpmailer_base . '/Exception.php') &&
        file_exists($phpmailer_base . '/PHPMailer.php') &&
        file_exists($phpmailer_base . '/SMTP.php')
    );

    $email_body = "
        <p align='center'><strong>FOREVER STAR INDIA</strong></p>
        <p>Your One-Time Password (OTP) is:</p>
        <p>OTP: <strong>{$otp}</strong></p>
        <p>This OTP is valid for the next 10 minutes. Please do not share this code with anyone.</p>
        <p>If you did not request this OTP, please ignore this email.</p>
        <p>Thank you,<br>FOREVER STAR INDIA<br>Support Team</p>
    ";

    if ($has_phpmailer) {
        require $phpmailer_base . '/Exception.php';
        require $phpmailer_base . '/PHPMailer.php';
        require $phpmailer_base . '/SMTP.php';

        // Use fully-qualified class names to avoid parse error from 'use' in old PHP versions
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'care@fsia.in';
            $mail->Password   = 'jvof scfa pzev vudv';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom('donotreply@fsia.in', 'Forever Star India');
            $mail->addAddress($email, 'Applicant');
            $mail->isHTML(true);
            $mail->Subject    = 'Your OTP for Verification - Forever Star India';
            $mail->Body       = $email_body;
            $mail->send();
            $email_sent = true;
        } catch (\Exception $e) {
            // PHPMailer failed — fall through to native mail()
            $email_sent = false;
        }
    }

    // ─── 3. FALLBACK: NATIVE mail() ──────────────────────────────────────────
    if (!$email_sent) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: Forever Star India <donotreply@fsia.in>\r\n";
        $result   = @mail($email, 'Your OTP for Verification - Forever Star India', $email_body, $headers);
        $email_sent = (bool)$result;
    }
}

// ─── RESPOND ──────────────────────────────────────────────────────────────────
echo json_encode([
    'success'        => true,
    'message'        => 'OTP sent successfully.',
    'whatsapp_sent'  => $whatsapp_sent,
    'email_sent'     => $email_sent,
    // REMOVE THIS IN PRODUCTION — only for local dev debugging:
    'dev_otp'        => (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], ['127.0.0.1:8000', 'localhost', 'localhost:8000'])) ? $otp : null,
]);
exit;
