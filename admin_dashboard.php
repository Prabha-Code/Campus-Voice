<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Interactive</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1f4037, #99f2c8);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            width: 350px;
            text-align: center;
            color: #fff;
        }
        h2 {
            margin-bottom: 20px;
        }
        .dashboard-links {
            display: flex;
            flex-direction: column;
        }
        .dashboard-links a {
            text-decoration: none;
            background: #ff6b6b;
            color: #fff;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
            display: block;
        }
        .dashboard-links a:hover {
            background: #ff4757;
        }
        .logout {
            background: #ffcc00;
            color: #333;
        }
        .logout:hover {
            background: #e6b800;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>👨‍💻 Admin Dashboard</h2>
        <div class="dashboard-links">
            <a href="manage_feedback.php">📋 Manage Feedback</a>
            <a href="manage_complaints1.php">⚠️ Manage Complaints</a>
            <a href="logout.php" class="logout">🚪 Logout</a>
        </div>
    </div>
</body>
</html>


