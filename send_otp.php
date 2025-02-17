<?php
session_start();
include "db.php"; // Database connection
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 // Install PHPMailer using Composer: `composer require phpmailer/phpmailer`

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    
    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in the database for the user
    $query = "UPDATE users SET otp='$otp' WHERE email='$email'";
    if (!mysqli_query($conn, $query)) {
        die("Database error: " . mysqli_error($conn));
    }

    // Send OTP via Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; 
        $mail->Password = 'your-email-password'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'CIT Complaint System');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP is: $otp";

        $mail->send();
        echo "OTP sent successfully!";
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>

