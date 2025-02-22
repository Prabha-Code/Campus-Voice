<?php
include 'db.php'; // Include database connection

// Handle the AJAX POST request for status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];

    // Validate and escape input to prevent SQL injection
    $complaint_id = $conn->real_escape_string($complaint_id);
    $status = $conn->real_escape_string($status);

    // Update the complaint status in the database
    $update_sql = "UPDATE complaints SET status = '$status' WHERE complaint_id = '$complaint_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Status updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    exit; // Stop further processing after AJAX response
}

// Fetch complaints and other necessary data from the database
$category_sql = "SELECT DISTINCT category FROM complaints";
$category_result = $conn->query($category_sql);
$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Get filter values (category and date)
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All'; // Default to 'All'
$date_filter = isset($_GET['date']) ? $_GET['date'] : ''; // Default to empty

// Build the SQL query to fetch complaints based on the selected filters
$complaints_sql = "SELECT complaint_id, category, description, status, created_at FROM complaints";

// Apply category filter
if ($category_filter !== 'All') {
    $complaints_sql .= " WHERE category = '$category_filter'";
}

// Apply date filter
if ($date_filter) {
    if ($category_filter !== 'All') {
        $complaints_sql .= " AND DATE(created_at) = '$date_filter'";
    } else {
        $complaints_sql .= " WHERE DATE(created_at) = '$date_filter'";
    }
}

$complaints_sql .= " ORDER BY created_at DESC";
$result = $conn->query($complaints_sql);

// Fetch data for the bar chart (category count by status)
$category_count_sql = "SELECT category, status, COUNT(*) as count FROM complaints";
if ($category_filter !== 'All') {
    $category_count_sql .= " WHERE category = '$category_filter'";
}

if ($date_filter) {
    if ($category_filter !== 'All') {
        $category_count_sql .= " AND DATE(created_at) = '$date_filter'";
    } else {
        $category_count_sql .= " WHERE DATE(created_at) = '$date_filter'";
    }
}

$category_count_sql .= " GROUP BY category, status";

$category_count_result = $conn->query($category_count_sql);

$category_data = [];
while ($row = $category_count_result->fetch_assoc()) {
    $category = $row['category'];
    $status = $row['status'];
    $count = $row['count'];

    if (!isset($category_data[$category])) {
        $category_data[$category] = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0];
    }

    $category_data[$category][$status] = $count;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Complaint Status</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        .table-container {
            width: 45%;
            overflow-x: auto;
        }
        .chart-container {
            width: 50%;
        }
    </style>
</head>
<body>
    <h2>Complaint Status Management</h2>

    <!-- Category Dropdown to Filter Data -->
    <label for="category_filter">Filter by Category: </label>
    <select id="category_filter" name="category_filter">
        <option value="All" <?php echo ($category_filter == 'All') ? 'selected' : ''; ?>>All</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category; ?>" <?php echo ($category_filter == $category) ? 'selected' : ''; ?>><?php echo $category; ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Date Filter: Calendar for Selecting a Date -->
    <label for="date_filter">Select Date: </label>
    <input type="date" id="date_filter" name="date_filter" value="<?php echo $date_filter; ?>">

    <button id="apply_filters">Apply Filters</button>

    <div class="container">
        <div class="table-container">
            <table border="1">
                <thead>
                    <tr>
                        <th>Date Submitted</th>
                        <th>Complaint ID</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Current Status</th>
                        <th>Updated Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("d-m-Y", strtotime($row['created_at'])); ?></td>
                            <td><?php echo $row['complaint_id']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <!-- Dropdown for changing status -->
                                <select class="status-dropdown" data-complaint-id="<?php echo $row['complaint_id']; ?>">
                                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="In Progress" <?php echo ($row['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?php echo ($row['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="chart-container">
            <canvas id="complaintChart"></canvas>
        </div>
    </div>

    <script>
        // Update the chart and table based on category and date filter
        $(document).on('click', '#apply_filters', function() {
            var selectedCategory = $('#category_filter').val();
            var selectedDate = $('#date_filter').val();
            var url = "admin.php?category=" + selectedCategory + "&date=" + selectedDate;
            window.location.href = url; // Reload page with selected filters
        });

        // Handle status change without button using AJAX
        $(document).on('change', '.status-dropdown', function() {
            var complaint_id = $(this).data('complaint-id');
            var status = $(this).val();

            // Send AJAX request to update status
            $.ajax({
                url: 'admin.php', // Same page to handle the POST request
                type: 'POST',
                data: {
                    complaint_id: complaint_id,
                    status: status
                },
                success: function(response) {
                    alert('Status updated successfully!');
                    location.reload(); // Refresh page to show the updated status
                },
                error: function() {
                    alert('Error updating status.');
                }
            });
        });

        // Data for the Bar Chart
        const categoryData = <?php echo json_encode($category_data); ?>;

        // Prepare the labels (categories)
        const categories = Object.keys(categoryData);

        // Prepare the datasets for each status
        const pendingData = categories.map(category => categoryData[category]['Pending'] || 0);
        const inProgressData = categories.map(category => categoryData[category]['In Progress'] || 0);
        const resolvedData = categories.map(category => categoryData[category]['Resolved'] || 0);

        // Chart.js configuration
        const ctx = document.getElementById('complaintChart').getContext('2d');
        const complaintChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [
                    {
                        label: 'Pending',
                        data: pendingData,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'In Progress',
                        data: inProgressData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Resolved',
                        data: resolvedData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
