<?php
include 'db.php';

// Get category and status filters
$category = isset($_GET['category']) ? $_GET['category'] : "All";
$status = isset($_GET['status']) ? $_GET['status'] : "All";

// Construct SQL query for complaints based on category and status
$complaints_sql = "SELECT complaint_id, category, description, status, created_at FROM complaints";
$conditions = [];

if ($category != "All") {
    $conditions[] = "category = '$category'";
}
if ($status != "All") {
    $conditions[] = "status = '$status'";
}

if (count($conditions) > 0) {
    $complaints_sql .= " WHERE " . implode(" AND ", $conditions);
}

$complaints_sql .= " ORDER BY created_at DESC";

$complaints_result = $conn->query($complaints_sql);
$complaints = [];

while ($row = $complaints_result->fetch_assoc()) {
    $complaints[] = $row;
}

// Fetch updated complaint counts for chart
$analytics_sql = "SELECT category, COUNT(*) as count FROM complaints";
if ($category != "All") {
    $analytics_sql .= " WHERE category = '$category'";
}
if ($status != "All") {
    $analytics_sql .= " AND status = '$status'";
}

$analytics_sql .= " GROUP BY category";

$analytics_result = $conn->query($analytics_sql);
$chart_labels = [];
$chart_data = [];

while ($row = $analytics_result->fetch_assoc()) {
    $chart_labels[] = $row['category'];
    $chart_data[] = $row['count'];
}

// Send data as JSON
echo json_encode(["complaints" => $complaints, "chart_labels" => $chart_labels, "chart_data" => $chart_data]);
$conn->close();
?>
