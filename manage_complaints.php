<?php
session_start();
include "db.php"; // Database connection

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch complaints from the database
$query = "SELECT * FROM complaints ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Complaints</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #00b4db, #0083b0);
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: bold;
        }

        table {
            width: 90%;
            margin: auto;
            background: rgba(255, 255, 255, 0.2);
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        th {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .status-dropdown {
            padding: 8px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .status-dropdown:focus {
            outline: none;
        }

        .btn {
            background: #ff7f50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #ff5733;
        }
    </style>
</head>
<body>

    <h2>🔧 Manage Complaints</h2>
    <table border="0">
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Category</th>
            <th>Query</th>
            <th>Assigned Email</th>
            <th>Status</th>
            <th>Update Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $assigned_email = ($row['category'] == 'Academic') ? 'academic@citchennai.net' : 'nonacademic@citchennai.net';
        ?>
            <tr id="row-<?php echo $row['id']; ?>">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['query_text']; ?></td>
                <td><?php echo $assigned_email; ?></td>
                <td id="status-<?php echo $row['id']; ?>" style="font-weight: bold; color: 
                    <?php echo ($row['status'] == "Pending") ? "red" : (($row['status'] == "Under Process") ? "orange" : "green"); ?>;">
                    <?php echo $row['status']; ?>
                </td>
                <td>
                    <form>
                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                        <select name="status" class="status-dropdown" data-id="<?php echo $row['id']; ?>">
                            <option value="Pending" <?php if ($row['status'] == "Pending") echo "selected"; ?>>Pending</option>
                            <option value="Under Process" <?php if ($row['status'] == "Under Process") echo "selected"; ?>>Under Process</option>
                            <option value="Resolved" <?php if ($row['status'] == "Resolved") echo "selected"; ?>>Resolved</option>
                        </select>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <script>
        $(document).ready(function () {
            $(".status-dropdown").change(function () {
                var complaint_id = $(this).data("id");
                var new_status = $(this).val();

                $.ajax({
                    url: "update_status.php",
                    type: "POST",
                    data: { complaint_id: complaint_id, status: new_status },
                    success: function (response) {
                        if (response == "success") {
                            var statusCell = $("#status-" + complaint_id);
                            statusCell.text(new_status);
                            if (new_status == "Pending") {
                                statusCell.css("color", "red");
                            } else if (new_status == "Under Process") {
                                statusCell.css("color", "orange");
                            } else if (new_status == "Resolved") {
                                statusCell.css("color", "green");
                            }
                        }
                    }
                });
            });
        });
    </script>

    <a href="admin_dashboard.php" class="btn">🏠 Return to Dashboard</a>

</body>
</html>
