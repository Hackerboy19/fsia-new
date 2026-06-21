<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
ini_set('display_errors', 0);
error_reporting(0);
include('config.php');

use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception;

$has_phpmailer = false;
if (file_exists('PHPMailer/src/Exception.php') && file_exists('PHPMailer/src/PHPMailer.php') && file_exists('PHPMailer/src/SMTP.php')) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    $has_phpmailer = true;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = preg_replace('/\D/', '', $_REQUEST['mobile'] ?? '');

if(empty($mobile)) {
	$res['data'][]=array(
		'Massage' => 'Mobile number is required.',
            );
}else{
   
 $otp = rand(100000, 999999); 
 if (session_status() === PHP_SESSION_NONE) {
     session_start();
 }
$_SESSION['otp'] = $otp;
$_SESSION['mobile'] = $mobile;
$_SESSION['email'] = $_REQUEST['email'] ?? '';
$_SESSION['otp_time'] = time();

 if (!defined('SQLSRV_CURSOR_KEYSET')) {
    define('SQLSRV_CURSOR_KEYSET', 2);
}
 
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$params = array();
if(isset($_REQUEST['mobile'])){
	
		$mobile_cleaned = preg_replace('/\D/', '', $_REQUEST['mobile']);
		$clickchat_mobile = $mobile_cleaned;
		if (strlen($mobile_cleaned) === 10 && in_array($mobile_cleaned[0], ['6', '7', '8', '9'])) {
			$clickchat_mobile = '91' . $mobile_cleaned;
		}

		if (php_sapi_name() !== 'cli') {
 		$curls = curl_init();
	curl_setopt_array($curls, array(									
CURLOPT_URL =>"https://auto.clickchat.io/webhook/682db5d3169b2264d88b3af5?number=".$clickchat_mobile."&message=otp,".$otp ,							
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36" // Here we add the header
			),								
		));
		curl_setopt($curls, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curls, CURLOPT_SSL_VERIFYPEER, FALSE);
		 $response_inner_pages = curl_exec($curls);
	     $err = curl_error($curls);
		}
}	


if(isset($_REQUEST['email']) && $has_phpmailer && php_sapi_name() !== 'cli'){
$message1="<p align='center'><strong>FOREVER STAR INDIA</strong></p>

<p>Your One-Time Password (OTP) is:</p>
<p>OTP: ".$otp."</p>

<p>This OTP is valid for the next 10 minutes. Please do not share this code with anyone.</p>
<p>If you did not request this OTP, please ignore this email.</p>

<p>Thank you,  <br>
FOREVER STAR INDIA <br>
Support Team
   </p>";	
	






$to1= $_REQUEST['email'];
$mail = new PHPMailer(true);                              
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'care@fsia.in';
    $mail->Password = 'jvof scfa pzev vudv';       
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('donotreply@fsia.in', "Forever Star India");
    $mail->addAddress($to1, "Receiver");
	$mail->isHTML(true);    
    $mail->Subject = 'Your OTP for Verification';
    $mail->Body    = $message1;
    $mail->send();



	
}
?>			
		
			

            		
			
			
			
			
			
			
			
<?php  } } ?>
