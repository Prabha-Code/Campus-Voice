<?php
include "db.php"; // Database connection

if (isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['status'];

    $update_query = "UPDATE complaints SET status='$new_status' WHERE id='$complaint_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
