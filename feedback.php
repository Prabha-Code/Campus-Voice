<?php
include('db.php'); // Connect to the database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback | Interactive UI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #00b4db, #0083b0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .feedback-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            width: 400px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .feedback-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .input-label {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-top: 15px;
            text-align: left;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.2);
            border-bottom: 2px solid #fff;
        }

        .textarea-field {
            resize: none;
            height: 100px;
        }

        .feedback-submit {
            width: 100%;
            background: #ff7f50;
            color: white;
            font-size: 16px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
            font-weight: bold;
        }

        .feedback-submit:hover {
            background: #ff5733;
        }
    </style>
</head>
<body>

    <div class="feedback-container">
        <h2 class="feedback-title">💬 Submit Your Feedback</h2>

        <form action="process_feedback.php" method="POST">
            <!-- Name Input -->
            <label for="name" class="input-label">👤 Your Name:</label>
            <input type="text" id="name" name="name" class="input-field" required>

            <!-- Email Input -->
            <label for="email" class="input-label">📧 Your Email:</label>
            <input type="email" id="email" name="email" class="input-field" required>

            <!-- Feedback Textarea -->
            <label for="feedback" class="input-label">✍️ Your Feedback:</label>
            <textarea id="feedback" name="feedback" class="input-field textarea-field" required></textarea>

            <!-- Submit Button -->
            <button type="submit" class="feedback-submit">✅ Submit Feedback</button>
        </form>
    </div>

</body>
</html>
