<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE complaint_id=?");
    $stmt->bind_param("ss", $status, $complaint_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>
