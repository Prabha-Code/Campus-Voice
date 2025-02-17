<?php
include('db.php');

// Secure Query Execution
$stmt = $conn->prepare("SELECT name, email, feedback, submitted_at FROM feedback ORDER BY submitted_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
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
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        th {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.2);
            transition: 0.3s;
        }

        .btn {
            background: #ff7f50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn:hover {
            background: #ff5733;
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

    <a href="dashboard.php" class="btn">🏠 Return to Dashboard</a>

    <h2>📢 Student Feedback</h2>
    
    <table border="0">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Feedback</th>
            <th>Submitted At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
