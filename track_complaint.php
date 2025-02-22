<?php
include 'db.php';
$complaint = null;
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = $_POST['complaint_id'];

    // Check if the complaint ID exists in the database
    $sql = "SELECT complaint_id, status FROM complaints WHERE complaint_id = '$complaint_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $complaint = $result->fetch_assoc();
    } else {
        $error_message = "Invalid Complaint ID. Please enter the correct ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaint</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #1a242f, #212f3c); /* Dark gradient background */
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(0, 0, 0, 0.7); /* Dark semi-transparent container */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #ecf0f1; /* Light color for heading */
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"] {
            padding: 12px;
            margin: 12px 0;
            border-radius: 5px;
            border: 1px solid #7f8c8d; /* Dark border */
            font-size: 16px;
            background-color: #34495e; /* Dark input background */
            color: white;
        }
        input[type="text"]:focus {
            border-color: #f39c12; /* Bright focus border color */
            outline: none;
        }
        button {
            padding: 12px;
            background-color: #f39c12; /* Bright button color */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e67e22; /* Bright button hover effect */
        }
        .status {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            color: #ecf0f1; /* Light color for status text */
        }
        .error-message {
            color: #e74c3c; /* Red error message */
            font-size: 16px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Track Your Complaint</h2>
    
    <?php if ($error_message) { ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php } ?>

    <form action="track_complaint.php" method="POST">
        <label for="complaint_id">Enter Complaint ID:</label>
        <input type="text" name="complaint_id" id="complaint_id" required placeholder="Enter your complaint ID">
        <button type="submit">Check Status</button>
    </form>

    <?php if ($complaint) { ?>
        <div class="status">
            <h3>Complaint ID: <?php echo $complaint['complaint_id']; ?></h3>
            <p>Status: <?php echo $complaint['status']; ?></p>
        </div>
    <?php } ?>
</div>

</body>
</html>
