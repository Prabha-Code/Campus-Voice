<?php
session_start();
include "db.php"; // Database connection

// Check if complaint ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$complaint_id = $_GET['id'];

// Include Composer's autoloader to load PHPMailer classes
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch complaint details from the database
$stmt = $conn->prepare("SELECT subcategory, query_text, created_at FROM complaints WHERE complaint_id = ?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    die("Complaint not found.");
}

$category = $complaint['subcategory'];
$query_text = $complaint['query_text'];
$created_at = date("d M Y, H:i", strtotime($complaint['created_at']));

// Build the email message content
$message = "Dear Support Team,\n\n"
         . "The following complaint has been pending for over 30 minutes without any response:\n\n"
         . "Complaint ID: $complaint_id\n"
         . "Category: $category\n"
         . "Complaint Details: $query_text\n"
         . "Submission Date: $created_at\n\n"
         . "No response has been received yet. Please review and take the necessary action.\n\n"
         . "Best Regards,\n"
         . "Automated Escalation System";

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sudharsanam483@gmail.com';    // Your Gmail address
    $mail->Password   = 'yxpp rjqd gdqz zbfo';         // Your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender and recipient settings
    $mail->setFrom('noreply@example.com', 'Automated Escalation System');
    $mail->addAddress('prabhaa.aids2023@citchennai.net'); // Replace with the admin's email address

    // Set email format to plain text and add content
    $mail->isHTML(false);
    $mail->Subject = '⚠️ Complaint Escalation Alert';
    $mail->Body    = $message;

    // Send the email
    $mail->send();
    echo "<script>alert('Escalation email sent successfully.'); window.location.href='view_status.php';</script>";
} catch (Exception $e) {
    echo "Failed to send escalation email. Error: {$mail->ErrorInfo}";
}
?>