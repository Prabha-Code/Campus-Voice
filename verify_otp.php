<?php
session_start();
include "db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $otp = mysqli_real_escape_string($conn, $_POST["otp"]);

    $query = "SELECT * FROM users WHERE email='$email' AND otp='$otp'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // OTP Verified Successfully
        $_SESSION["user"] = $email;

        // Clear OTP after verification
        $update = "UPDATE users SET otp=NULL WHERE email='$email'";
        mysqli_query($conn, $update);

        echo "OTP verified successfully! Redirecting...";
    } else {
        echo "Invalid OTP!";
    }
}
?>

