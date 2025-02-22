<?php
include 'db.php';

$chart_sql = "SELECT subcategory, status, COUNT(*) as count FROM complaints GROUP BY subcategory, status";
$chart_result = $conn->query($chart_sql);
$data = [];
while ($row = $chart_result->fetch_assoc()) {
    $data[$row['subcategory']][$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Complaint Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Complaint Statistics</h2>
    <a href="manage_complaints1.php">Back to Complaints</a>
    <canvas id="complaintChart"></canvas>
    <script>
        var ctx = document.getElementById('complaintChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($data)); ?>,
                datasets: [
                    { label: 'Pending', backgroundColor: 'red', data: <?php echo json_encode(array_column($data, 'Pending', 0)); ?> },
                    { label: 'In Progress', backgroundColor: 'blue', data: <?php echo json_encode(array_column($data, 'In Progress', 0)); ?> },
                    { label: 'Resolved', backgroundColor: 'green', data: <?php echo json_encode(array_column($data, 'Resolved', 0)); ?> }
                ]
            }
        });
    </script>
</body>
</html>