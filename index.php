<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Voice - CIT</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: #f5f5f5;
            color: white;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #192231;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar .logo-container {
            display: flex;
            align-items: center;
        }

        .navbar .logo {
            height: 50px;
            margin-right: 15px;
        }

        .navbar .logo-text {
            font-size: 22px;
            font-weight: bold;
            color: white;
        }

        .navbar ul {
            list-style: none;
            display: flex;
        }

        .navbar ul li {
            margin: 0 15px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s;
            cursor: pointer;
        }

        .navbar ul li a:hover {
            color: #ff7582;
        }

        /* Home Section */
        .login-box {
    width: 300px;
    padding: 20px;
    margin: 100px auto;
    text-align: center;
    border: 2px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

/* Style for additional contents inside the box */
.login-box .content {
    margin-bottom: 15px;
    font-size: 16px;
    color: #333;
}

/* Style for the login button */
.login-button {
    display: block;
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.login-button:hover {
    background-color: #0056b3;
}
        .welcome {
            text-align: center;
            padding: 100px 20px;
            margin-top: 80px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: url('college-background.jpg') center/cover no-repeat;
        }

        .welcome h1 {
            font-size: 40px;
            color: white;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .welcome p {
            font-size: 20px;
            margin-top: 10px;
            opacity: 0.9;
            color: white;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            background: #192231;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin: 10px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn:hover {
            background: #e67e22;
        }

        /* About Us Section */
        .about {
            padding: 100px 20px;
            background: linear-gradient(to right,#D1E8E2, #A0522D);
            text-align: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            color: white;
        }

        .about-content {
            display: flex;
            width: 80%;
            justify-content: space-between;
            align-items: center;
        }

        .about-left {
            text-align: left;
            width: 40%;
            display: flex;
            align-items: center;
        }

        .about-left img {
            width: 100px;
            margin-right: 15px;
        }

        .about-left .college-info {
            display: flex;
            flex-direction: column;
        }

        .about-left h2 {
            font-size: 22px;
        }

        .about-left p {
            font-size: 14px;
            color: #f1c40f;
        }

        .about-right {
            text-align: left;
            width: 50%;
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            color: #fff;
        }

        .about-right h2 {
            font-size: 28px;
        }

        .about-right p {
            font-size: 18px;
            margin-top: 10px;
            line-height: 1.5;
        }

        .quote {
            font-style: italic;
            margin-top: 10px;
            font-size: 16px;
            color: #f1c40f;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 12px;
            margin-top: 20px;
            background: #333;
            color: white;
            border-radius: 0 0 12px 12px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo-container">
            <img src="logo.jpg" alt="CIT Logo" class="logo">
            <span class="logo-text">Campus Voice</span>
        </div>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>

    <!-- Welcome Section -->
    <div class="welcome" id="home">
        <h1>Welcome to the College Complaint & Feedback System</h1>
        <p>Your voice matters! Submit your complaints and feedback effortlessly.</p>
        <div class="buttons">
            <a href="login.php" class="btn">Login as User</a>
            <a href="admin_login.php" class="btn">Admin Login</a>
        </div>
    </div>

    <!-- About Section -->
    <div class="about" id="about">
        <div class="about-content">
            <div class="about-left">
                <img src="logo.jpg" alt="CIT Logo">
                <div class="college-info">
                    <h2>Chennai Institute of Technology</h2>
                    <p>Chennai - 600069, Tamil Nadu, India</p>
                </div>
            </div>
            <div class="about-right">
                <h2>Empowering Student & Teacher Voices</h2>
                <p>Campus Voice is an online platform where students and teachers can submit complaints and feedback directly to the authorities.</p>
                <p class="quote">"Your voice shapes the future. Speak up for change!"</p>
            </div>
        </div>
    </div>
</body>
</html>
