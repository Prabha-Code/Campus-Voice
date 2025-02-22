<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "complaint_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for the bar chart
$sql = "SELECT 
            MONTH(created_at) AS month, 
            COUNT(*) AS total, 
            SUM(status = 'resolved') AS resolved, 
            SUM(status = 'pending') AS pending 
        FROM complaints 
        GROUP BY MONTH(created_at)";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>