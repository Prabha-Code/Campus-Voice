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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Flexbox Layout for Dashboard */
        .dashboard-layout {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .left-section {
            flex: 1;
            max-width: 50%;
        }

        .right-section {
            flex: 1;
            max-width: 50%;
            display:flex;
            align-items: center;
            justify-content: center;
            
            
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
            flex-direction: column;
            gap: 15px;
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
        .chatbot-icon {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #007bff; /* Change color if needed */
        color: white;
        padding: 15px;
        border-radius: 50%;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 24px;
        transition: background-color 0.3s ease;
    }
        /* Bar Chart Section */
        .dashboard-section {
            background: linear-gradient(to right,rgb(222, 231, 238),rgb(247, 152, 108));
            
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            color: black;
            width: 90%;
        }

        canvas {
            max-width: 100%;
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

        <!-- Flexbox Layout -->
        <div class="dashboard-layout">
            <!-- Left Section: Dashboard Options -->
            <div class="left-section">
                <div class="dashboard-options">
                    <a href="submit_complaint.php"><i class="fas fa-edit"></i> Submit Complaint</a>
                    <a href="view_status.php"><i class="fas fa-eye"></i> View Status</a>
                    <a href="feedback.php"><i class="fas fa-comment"></i> Submit Feedback</a>
                    <a href="view_ratings.php"><i class="fas fa-star"></i> View Ratings</a>
                    
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>

                <div class="quote-box" id="quoteBox">"Your voice shapes the future. Speak up for change!"</div>
            </div>
           
            <!-- Right Section: Bar Chart -->
            <div class="right-section">
                <div class="dashboard-section">
                    <h2>Complaints Analysis</h2>
                    <canvas id="complaintsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <a href="chatbot.html" class="chatbot-icon">
    <i class="fas fa-robot"></i>
</a>
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

        // Bar Chart Data
        function loadChart() {
        fetch('fetch_complaints.php')
        .then(response => response.json())
        .then(data => {
            // Array to convert month number to month name
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            // Map the data to extract labels (Month Names) and complaint stats
            let labels = data.map(d => monthNames[d.month - 1]); // Convert month number to month name
            let totalComplaints = data.map(d => d.total);
            let resolvedComplaints = data.map(d => d.resolved);
            let pendingComplaints = data.map(d => d.pending);


                let ctx = document.getElementById('complaintsChart').getContext('2d');
                if (window.myChart) window.myChart.destroy();

                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Total Complaints', data: totalComplaints, backgroundColor: 'rgba(255, 99, 132, 0.5)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 },
                            { label: 'Resolved Complaints', data: resolvedComplaints, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
                            { label: 'Pending Complaints', data: pendingComplaints, backgroundColor: 'rgba(255, 206, 86, 0.5)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 1 }
                        ]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });
            });
        }

        // Initial Calls
        updateGreeting();
        loadChart();
        setInterval(loadChart, 10000); // Refresh chart every 10 seconds
    </script>

</body>
</html>

<!-- Chatbot Icon (Fixed at Bottom Right) -->
<div class="chatbot-icon" onclick="toggleChat()" style="cursor: pointer; position: fixed; bottom: 20px; right: 20px; background: #007bff; color: white; padding: 15px; border-radius: 50%; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); font-size: 24px;">
    <i class="fas fa-robot"></i>
</div>

<!-- Chatbot Popup (Fixed on the Right) -->
<div class="chat-popup" id="chatPopup" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 300px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #ccc;">
    <div class="chat-header" style="background: #007bff; color: white; padding: 10px; text-align: center; font-size: 18px; position: relative;">
        Chatbot
        <span class="close-btn" onclick="toggleChat()" style="position: absolute; right: 10px; top: 5px; cursor: pointer; font-size: 20px;">&times;</span>
    </div>
    <div class="chat-body" id="chatBody" style="padding: 10px; height: 250px; overflow-y: auto; font-size: 14px; background:rgb(2, 2, 2);">
        <p><strong>Bot:</strong> Hello! How can I assist you?</p>
    </div>
    <div class="chat-input" style="display: flex; border-top: 1px solid #ccc;">
        <input type="text" id="userInput" placeholder="Type a message..." style="flex: 1; padding: 10px; border: none; outline: none;background-color:gray;color:white; ">
        <button onclick="sendMessage()" style="padding: 10px; background: #007bff; color: white; border:none;cursor: pointer;">Send</button>
    </div>
</div>

<script>
    function toggleChat() {
        var chatPopup = document.getElementById("chatPopup");
        chatPopup.style.display = chatPopup.style.display === "block" ? "none" : "block";
    }

    function sendMessage() {
        var userInput = document.getElementById("userInput").value.trim();
        var chatBody = document.getElementById("chatBody");

        if (userInput === "") return;

        var userMessage = `<p><strong>You:</strong> ${userInput}</p>`;
        chatBody.innerHTML += userMessage;

        // Send message to testing.py backend
        fetch("http://127.0.0.1:5000/chat", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ message: userInput })
        })
        .then(response => response.json())
        .then(data => {
            var botMessage = `<p><strong>Bot:</strong> ${data.response}</p>`;
            chatBody.innerHTML += botMessage;
            chatBody.scrollTop = chatBody.scrollHeight;
        })
        .catch(error => {
            console.error("Error:", error);
            var botMessage = `<p><strong>Bot:</strong> Failed to connect to chatbot. Is the server running?</p>`;
            chatBody.innerHTML += botMessage;
        });

        document.getElementById("userInput").value = "";
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Send message on pressing Enter
    document.getElementById("userInput").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
            event.preventDefault(); // Prevents adding a new line in input
        }
    });
</script>
