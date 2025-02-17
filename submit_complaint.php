<?php
session_start();
include "db.php"; // Ensure database connection is included

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_complaint"])) {
    $category = $_POST["category"];
    $query_text = mysqli_real_escape_string($conn, $_POST["query"]);
    $user_id = $_SESSION["user_id"];
    $assigned_email = ($category == "academic") ? "academic@citchennai.net" : "nonacademic@citchennai.net";

    $stmt = $conn->prepare("INSERT INTO complaints (user_id, category, query_text, assigned_email, status) VALUES (?, ?, ?, ?, 'Under Review')");
    $stmt->bind_param("isss", $user_id, $category, $query_text, $assigned_email);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Complaint submitted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Error submitting complaint. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint | Interactive</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #4A00E0, #8E2DE2);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(43, 5, 5, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
        }
        select, textarea, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }
        select, textarea {
            background: rgba(57, 3, 3, 0.2);
            color: #fff;
            outline: none;
        }
        textarea {
            height: 100px;
            resize: none;
        }
        button {
            background: #ff6b6b;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        button:hover {
            background: #ff4757;
        }
        select option {
            background: #333;
            color: #fff;
        }
        .dashboard-link {
            display: block;
            margin-top: 15px;
            color: #FFD700;
            text-decoration: none;
            font-size: 14px;
        }
        .dashboard-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Complaint</h2>
        <form id="complaintForm" method="POST">
            <label>Category:</label>
            <select name="category" required>
                <option value="academic">Academic</option>
                <option value="non-academic">Non-Academic</option>
            </select>
            <textarea name="query" id="query" placeholder="Describe your complaint..." required></textarea>
            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>
        <a href="dashboard.php" class="dashboard-link">🔙 Back to Dashboard</a>
    </div>
</body>
</html>