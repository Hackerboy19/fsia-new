<?php
/**
 * OTP & SMTP Diagnostic Tool
 * Visit http://127.0.0.1:8000/php/test_otp.php in your browser to check for issues.
 */
ini_set('display_errors', 1);
ini_set('html_errors', 1);
error_reporting(E_ALL);

echo "<h1>OTP & SMTP Diagnostic Tool</h1>";

// 1. Check PHP Version
echo "<h2>1. PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// 2. Check PHPMailer availability
echo "<h2>2. PHPMailer Files</h2>";
$has_phpmailer = true;
$files = [
    'PHPMailer/src/Exception.php',
    'PHPMailer/src/PHPMailer.php',
    'PHPMailer/src/SMTP.php'
];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ Found: $file<br>";
    } else {
        echo "❌ Missing: $file<br>";
        $has_phpmailer = false;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($has_phpmailer) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
}

// 3. Test clickchat.io cURL request
echo "<h2>3. Testing clickchat.io Webhook Connection</h2>";
$clickchat_url = "https://auto.clickchat.io/webhook/682db5d3169b2264d88b3af5?number=919876543210&message=otp,test12";
echo "Sending test cURL to clickchat...<br>";

$curls = curl_init();
curl_setopt_array($curls, array(									
    CURLOPT_URL => $clickchat_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "user-agent: Mozilla/5.0"
    ),								
));
curl_setopt($curls, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curls, CURLOPT_SSL_VERIFYPEER, FALSE);
$response = curl_exec($curls);
$err = curl_error($curls);
$http_code = curl_getinfo($curls, CURLINFO_HTTP_CODE);
curl_close($curls);

if ($err) {
    echo "❌ cURL Error: " . htmlspecialchars($err) . "<br>";
} else {
    echo "✅ Connection Success (HTTP $http_code)<br>";
    echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br>";
}

// 4. Test SMTP connection
echo "<h2>4. Testing Gmail SMTP Authentication</h2>";
if (!$has_phpmailer) {
    echo "❌ Skipping SMTP test: PHPMailer is not available.<br>";
} else {
    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 3; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            echo htmlspecialchars($str) . "<br>";
        };
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'care@fsia.in';
        $mail->Password = 'jvof scfa pzev vudv';       
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('donotreply@fsia.in', "Forever Star India");
        $mail->addAddress('care@fsia.in', "Test Receiver");
        $mail->isHTML(true);    
        $mail->Subject = 'SMTP Diagnostics test';
        $mail->Body    = "This is a diagnostic test email to verify SMTP configuration.";
        
        echo "<strong>SMTP Debug Log:</strong><br><div style='background:#f5f5f5; padding:10px; border:1px solid #ddd; font-family:monospace; font-size:12px;'>";
        $mail->send();
        echo "</div><br>✅ <strong>Email Sent Successfully!</strong> Connection and credentials are fully valid.<br>";
    } catch (Exception $e) {
        echo "</div><br>❌ <strong>SMTP Send Failed!</strong><br>";
        echo "PHPMailer Error: " . htmlspecialchars($mail->ErrorInfo) . "<br>";
        echo "Exception Message: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}
?>
