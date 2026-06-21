<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: text/plain');

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mobile = $_POST['mobile'] ?? '';
    $email  = $_POST['email'] ?? '';
    $otp    = $_POST['otp'] ?? '';

    $mobile = preg_replace('/\D/', '', $mobile);

    // =========================
    // 1. CHECK EMPTY
    // =========================
    if (empty($otp) || empty($mobile)) {
        echo "error";
        exit;
    }

    // =========================
    // 2. OTP EXPIRY CHECK (10 min)
    // =========================
    if (!isset($_SESSION['otp_time']) || (time() - $_SESSION['otp_time']) > 600) {
        echo "expired";
        exit;
    }

    // =========================
    // 3. VERIFY MOBILE MATCH
    // =========================
    if (!isset($_SESSION['mobile']) || $_SESSION['mobile'] != $mobile) {
        echo "error";
        exit;
    }

    // =========================
    // 4. OTP MATCH CHECK
    // =========================
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {

        // SUCCESS → mark verified
        $_SESSION['otp_verified'] = true;

        // optional cleanup
        unset($_SESSION['otp']);
        unset($_SESSION['otp_time']);
		$_SESSION['rmob'] = $_POST['mobile']; 
        echo "success";
        exit;

    } else {
        echo "error";
        exit;
    }

}
?>