<?php
include('db.php'); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

    $query = "INSERT INTO feedback (name, email, feedback) VALUES ('$name', '$email', '$feedback')";
    
    if (mysqli_query($conn, $query)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Feedback Submitted</title>
            <link rel="stylesheet" href="styles.css">
            <style>
                /* Centered Container */
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background: linear-gradient(to right, #4facfe, #00f2fe);
                    font-family: Arial, sans-serif;
                    margin: 0;
                }

                .success-box {
                    background: #fff;
                    padding: 30px;
                    width: 450px;
                    text-align: center;
                    border-radius: 10px;
                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
                    animation: fadeIn 0.5s ease-in-out;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(-20px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .success-icon {
                    font-size: 50px;
                    color: #28a745;
                    margin-bottom: 15px;
                }

                .success-text {
                    font-size: 20px;
                    color: #333;
                    margin-bottom: 10px;
                }

                .quote {
                    font-style: italic;
                    color: #555;
                    font-size: 16px;
                    margin-bottom: 15px;
                }

                .dashboard-btn {
                    background: #007bff;
                    color: white;
                    font-size: 16px;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: 0.3s;
                    text-decoration: none;
                    display: inline-block;
                }

                .dashboard-btn:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="success-box">
                <div class="success-icon">✅</div>
                <div class="success-text">Thank You for Your Feedback!</div>
                <div class="quote">"Your words help us grow and improve every day!"</div>
                <a href="dashboard.php" class="dashboard-btn">Return to Dashboard</a>
            </div>
            <script>
                // Redirect to dashboard after 5 seconds
                setTimeout(function() {
                    window.location.href = "dashboard.php";
                }, 5000);
            </script>
        </body>
        </html>';
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
