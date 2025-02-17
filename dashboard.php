<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Complaint & Feedback System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            text-align: center;
            transition: background 0.5s ease-in-out;
        }

        .container {
            margin-top: 80px;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            font-size: 28px;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
            font-weight: bold;
        }

        /* Greeting Message */
        .greeting {
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: bold;
            color: #f39c12;
        }

        /* Dashboard Options */
        .dashboard-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-options a {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f39c12;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: transform 0.3s ease, background 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .dashboard-options a:hover {
            background: #e67e22;
            transform: translateY(-5px);
        }

        .dashboard-options a i {
            margin-right: 10px;
            font-size: 22px;
        }

        /* Rotating Quotes */
        .quote-box {
            margin-top: 30px;
            font-size: 18px;
            font-style: italic;
            color: #f1c40f;
            animation: fadeIn 1.5s ease-in-out;
        }

        /* Dark Mode */
        .dark-mode {
            background: #121212;
            color: white;
        }

        .dark-mode .dashboard-options a {
            background: #444;
        }

        .dark-mode .dashboard-options a:hover {
            background: #555;
        }

        .toggle-dark {
            position: fixed;
            top: 10px;
            right: 20px;
            background: #f39c12;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .toggle-dark:hover {
            background: #e67e22;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <button class="toggle-dark" onclick="toggleDarkMode()">🌙 Toggle Dark Mode</button>

    <div class="container">
        <div class="greeting" id="greetingMessage"></div>

        <h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
        <p>Role: <?php echo ucfirst($_SESSION["user_type"]); ?></p>

        <div class="dashboard-options">
            <a href="submit_complaint.php"><i class="fas fa-edit"></i> Submit Complaint</a>
            <a href="view_status.php"><i class="fas fa-eye"></i> View Status</a>
            <a href="feedback.php"><i class="fas fa-comment"></i> Submit Feedback</a>
            <a href="chatbot.html"><i class="fas fa-robot"></i> Chat with Support</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="quote-box" id="quoteBox">"Your voice shapes the future. Speak up for change!"</div>
    </div>

    <script>
        // Greeting Message Based on Time
        function updateGreeting() {
            let hours = new Date().getHours();
            let greeting = "";
            if (hours < 12) {
                greeting = "Good Morning 🌅";
            } else if (hours < 18) {
                greeting = "Good Afternoon ☀️";
            } else {
                greeting = "Good Evening 🌙";
            }
            document.getElementById("greetingMessage").innerText = greeting;
        }

        // Rotating Quotes
        const quotes = [
            "Your voice shapes the future. Speak up for change!",
            "Transparency leads to trust. Share your feedback!",
            "Every complaint is a step towards improvement.",
            "Your opinion matters. Let’s make a difference together!",
            "Constructive feedback builds a stronger community!"
        ];

        function updateQuote() {
            let randomIndex = Math.floor(Math.random() * quotes.length);
            document.getElementById("quoteBox").innerText = quotes[randomIndex];
        }

        setInterval(updateQuote, 5000); // Change quote every 5 seconds

        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
        }

        // Remember Dark Mode Preference
        if (localStorage.getItem("darkMode") === "enabled") {
            document.body.classList.add("dark-mode");
        }

        // Initial Call
        updateGreeting();
    </script>

</body>
</html>
