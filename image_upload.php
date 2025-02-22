<?php
include "db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && isset($_POST["complaint_id"])) {
    $complaint_id = $_POST["complaint_id"];
    $image_name = $_FILES["image"]["name"];
    $image_tmp = $_FILES["image"]["tmp_name"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image_name);

    // Move file to uploads directory
    if (move_uploaded_file($image_tmp, $target_file)) {
        $query = "INSERT INTO complaint_images (complaint_id, image_name, image_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $complaint_id, $image_name, $target_file);
        
        if ($stmt->execute()) {
            echo "Image uploaded successfully!";
        } else {
            echo "Upload failed: " . $conn->error;
        }
    } else {
        echo "File move failed!";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="number" name="complaint_id" placeholder="Enter Complaint ID" required>
    <input type="file" name="image" required>
    <button type="submit">Upload</button>
</form>
