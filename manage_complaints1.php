<?php
include 'db.php'; // Include database connection

// Handle AJAX request for updating complaint status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];

    $complaint_id = $conn->real_escape_string($complaint_id);
    $status = $conn->real_escape_string($status);

    $update_sql = "UPDATE complaints SET status = '$status' WHERE complaint_id = '$complaint_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $conn->error;
    }
    exit;
}

// Fetch distinct categories
$category_sql = "SELECT DISTINCT category FROM complaints";
$category_result = $conn->query($category_sql);
if (!$category_result) {
    die("Error fetching categories: " . $conn->error);
}

$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Get filter values
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : 'All';
$subcategory_filter = isset($_GET['subcategory_filter']) ? $_GET['subcategory_filter'] : 'All';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';


// Fetch subcategories dynamically based on selected category
$subcategories = [];
if ($category_filter !== 'All') {
    $subcategory_sql = "SELECT DISTINCT subcategory FROM complaints WHERE category = '" . $conn->real_escape_string($category_filter) . "'";
    $subcategory_result = $conn->query($subcategory_sql);
    if ($subcategory_result) {
        while ($row = $subcategory_result->fetch_assoc()) {
            $subcategories[] = $row['subcategory'];
        }
    }
}

// Build SQL query for fetching complaints
$complaints_sql = "SELECT complaint_id, category, subcategory, query_text, status, created_at, image_path FROM complaints";
$conditions = [];

if ($category_filter !== 'All') {
    $conditions[] = "category = '" . $conn->real_escape_string($category_filter) . "'";
}
if ($subcategory_filter !== 'All') {
    $conditions[] = "subcategory = '" . $conn->real_escape_string($subcategory_filter) . "'";
}
if ($date_filter) {
    $conditions[] = "DATE(created_at) = '" . $conn->real_escape_string($date_filter) . "'";
}
if (!empty($conditions)) {
    $complaints_sql .= " WHERE " . implode(" AND ", $conditions);
}

$complaints_sql .= " ORDER BY created_at DESC";
$result = $conn->query($complaints_sql);
if (!$result) {
    die("Error fetching complaints: " . $conn->error);
}

// Fetch data for the chart (subcategory count by status)
$subcategory_count_sql = "SELECT subcategory, status, COUNT(*) as count FROM complaints";
if (!empty($conditions)) {
    $subcategory_count_sql .= " WHERE " . implode(" AND ", $conditions);
}
$subcategory_count_sql .= " GROUP BY subcategory, status";

$subcategory_count_result = $conn->query($subcategory_count_sql);
if (!$subcategory_count_result) {
    die("Error fetching chart data: " . $conn->error);
}

$subcategory_data = [];
while ($row = $subcategory_count_result->fetch_assoc()) {
    $subcategory = $row['subcategory'];
    $status = $row['status'];
    $count = $row['count'];

    if (!isset($subcategory_data[$subcategory])) {
        $subcategory_data[$subcategory] = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0,'Rejected'=>0];
    }
    $subcategory_data[$subcategory][$status] = $count;
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
        body {
    font-family: 'Arial', sans-serif;
    background-color:rgb(127, 195, 238);
    margin: 0;
    padding: 20px;
    text-align: center;
}

h2 {
    color: #333;
    font-size: 24px;
}

.container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-top: 10px;
}

.table-container {
    width: 100%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.chart-container {
    width: 40%;
    display: flex
;
    justify-content: center;
    align-items: center;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-top: 10%;
    margin-left: 30%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background:rgb(49, 130, 216);
    color: white;
}

tr:nth-child(even) {
    background: #f2f2f2;
}

.status-dropdown {
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

button {
    padding: 10px 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #218838;
}

    </style>
</head>
<body>
    <h2>Complaint Status Management</h2>

    <form id="filter_form" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-left: 275px;">
    <label for="category_filter">Filter by Category:</label>
    <select id="category_filter" name="category_filter">
        <option value="All">All</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" 
                <?php echo ($category_filter == $category) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="subcategory_filter">Filter by Subcategory:</label>
    <select id="subcategory_filter" name="subcategory_filter">
        <option value="All">All</option>
        <?php foreach ($subcategories as $subcategory): ?>
            <option value="<?php echo htmlspecialchars($subcategory, ENT_QUOTES, 'UTF-8'); ?>" 
                <?php echo ($subcategory_filter == $subcategory) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($subcategory, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="date_filter">Select Date:</label>
    <input type="date" id="date_filter" name="date_filter" value="<?php echo htmlspecialchars($date_filter, ENT_QUOTES, 'UTF-8'); ?>">

    <button type="submit">Apply Filters</button>
</form>

    <div class="container">
        <div class="table-container">
            <table border="1">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Complaint ID</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Description</th>
                        <th>Current Status</th>
                        <th>Updated Status</th>
		                <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("d-m-Y", strtotime($row['created_at'])); ?></td>
                            <td><?php echo $row['complaint_id']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['subcategory']; ?></td>
                            <td><?php echo $row['query_text']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <select class="status-dropdown" data-complaint-id="<?php echo $row['complaint_id']; ?>">
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Rejected">Reject</option>
                                </select>
                            </td>
                            <td> <?php $defaultImage = "uploads/default.jpg"; // Set your default image path here

    if (!empty($row['image_path'])) { 
        echo "<img src='" . $row['image_path'] . "' width='200px' height='100px'><br>"; 
    } else { 
        echo "<img src='" . $defaultImage . "' width='200px' height='100px'><br>"; 
    }?></td>
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
        const statusElements = document.querySelectorAll(".status-dropdown");
        document.querySelectorAll(".status-dropdown").forEach(dropdown => {
    function updateColor() {
        if (dropdown.value === "Resolved") {
            dropdown.style.backgroundColor = "green";
            dropdown.style.color = "white";
        } else if (dropdown.value === "In Progress") {
            dropdown.style.backgroundColor = "yellow";
            dropdown.style.color = "black";
        } else if (dropdown.value=="Rejected"){
            dropdown.style.backgroundColor="red";
            dropdown.style.color="white";
        } else {
            dropdown.style.backgroundColor = "orange";
            dropdown.style.color = "white";
        }
    }

    updateColor();

    dropdown.addEventListener("change", updateColor);
});


        $(document).ready(function() {
    $(".status-dropdown").change(function() {
        var complaint_id = $(this).data("complaint-id");
        var new_status = $(this).val();
        var $row = $(this).closest("tr");

        $.ajax({
            url: "manage_complaints1.php",
            type: "POST",
            data: { complaint_id: complaint_id, status: new_status },
            success: function(response) {
                alert(response); // Optional: Show success message
                $row.find("td:nth-child(6)").text(new_status); // Update the status in the table dynamically
            },
            error: function() {
                alert("Error updating status");
            }
        });
    });
});

            $("#filter_form").submit(function(event) {
                event.preventDefault();
                window.location.href = "manage_complaints1.php?category_filter=" + $("#category_filter").val() +
                                    "&subcategory_filter=" + $("#subcategory_filter").val() +
                                    "&date_filter=" + $("#date_filter").val();
            });


            new Chart(document.getElementById('complaintChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($subcategory_data)); ?>,
                    datasets: [
                        { 
                            label: 'Pending', 
                            backgroundColor: 'orange', 
                            data: <?php echo json_encode(array_column($subcategory_data, 'Pending')); ?> 
                        },
                        { 
                            label: 'In Progress', 
                            backgroundColor: 'blue', 
                            data: <?php echo json_encode(array_column($subcategory_data, 'In Progress')); ?> 
                        },
                        { 
                            label: 'Resolved', 
                            backgroundColor: 'green', 
                            data: <?php echo json_encode(array_column($subcategory_data, 'Resolved')); ?> 
                        },
                        { 
                            label: 'Rejected', 
                            backgroundColor: 'red', 
                            data: <?php echo json_encode(array_column($subcategory_data, 'Rejected')); ?> 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Complaints'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Subcategories'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

    </script>
</body>
</html>