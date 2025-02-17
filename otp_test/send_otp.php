<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If using Composer
session_start();

function sendOTP($email) {
    $otp = rand(100000, 999999); // Generate a 6-digit OTP
    $_SESSION['otp'] = $otp; // Store OTP in session
    $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 mins

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       ='smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sudharsanam483@gmail.com'; // Your email
        $mail->Password   = 'yxpp rjqd gdqz zbfo'; // App password or actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your-email@gmail.com', 'Your App Name');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<h3>Your OTP is: <b>$otp</b></h3><p>It is valid for 5 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    if (sendOTP($email)) {
        echo "OTP sent successfully!";
    } else {
        echo "Failed to send OTP.";
    }
}
?>
