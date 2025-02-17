<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Secure Deletion
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "deleted";
    exit();
}

// Secure Approval
if (isset($_POST['approve_id'])) {
    $id = intval($_POST['approve_id']);
    $stmt = $conn->prepare("UPDATE feedback SET status='approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "approved";
    exit();
}

// Fetch Feedback
$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Feedback</title>
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

        .btn {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            cursor: pointer;
            border: none;
        }

        .approve-btn {
            background: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background: #218838;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .dashboard-btn {
            background: #ff7f50;
            color: white;
            padding: 10px 20px;
            display: inline-block;
            margin-top: 20px;
        }

        .dashboard-btn:hover {
            background: #ff5733;
        }
    </style>
</head>
<body>

    <h2>📝 Manage Feedback</h2>
    <table border="0">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Feedback</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr id="row-<?php echo $row['id']; ?>">
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                <td>
                    <button class="btn approve-btn" onclick="approveFeedback(<?php echo $row['id']; ?>)">Approve</button>
                    <button class="btn delete-btn" onclick="deleteFeedback(<?php echo $row['id']; ?>)">Delete</button>
                </td>
            </tr>
        <?php } ?>
    </table>

    <script>
        function approveFeedback(id) {
            $.post("manage_feedback.php", { approve_id: id }, function(response) {
                if (response === "approved") {
                    alert("Feedback Approved!");
                    $("#row-" + id).fadeOut();
                }
            });
        }

        function deleteFeedback(id) {
            if (confirm("Are you sure you want to delete this feedback?")) {
                $.post("manage_feedback.php", { delete_id: id }, function(response) {
                    if (response === "deleted") {
                        alert("Feedback Deleted!");
                        $("#row-" + id).fadeOut();
                    }
                });
            }
        }
    </script>

    <a href="admin_dashboard.php" class="dashboard-btn">🏠 Return to Dashboard</a>

</body>
</html>
