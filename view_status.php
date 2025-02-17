<?php
session_start();
include "db.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Secure Query Execution
$stmt = $conn->prepare("SELECT id, category, query_text, status FROM complaints WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ff9966, #ff5e62);
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            margin: auto;
            background: rgba(255, 255, 255, 0.15);
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        th {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-weight: bold;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.2);
            transition: 0.3s;
        }

        .status {
            font-weight: bold;
            padding: 8px;
            border-radius: 5px;
            display: inline-block;
        }

        .pending { background: red; }
        .processing { background: orange; }
        .resolved { background: green; }

        .btn {
            background: #007bff;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn:hover {
            background: #0056b3;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <h2>📋 Your Complaint Status</h2>
    
    <table border="0">
        <tr>
            <th>Complaint ID</th>
            <th>Category</th>
            <th>Query</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['query_text']); ?></td>
                <td>
                    <span class="status 
                        <?php echo ($row['status'] == 'Pending') ? 'pending' : 
                                    (($row['status'] == 'Under Process') ? 'processing' : 'resolved'); ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
            </tr>
        <?php } ?>
    </table>

    <button class="btn" onclick="window.location.href='dashboard.php'">🏠 Return to Dashboard</button>

</body>
</html>
