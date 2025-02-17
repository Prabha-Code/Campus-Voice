<?php
session_start();
include "db.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $password = $_POST["password"];

        // ✅ Admin Hardcoded Credentials
        $admin_email = "admin@college.com";
        $admin_password = "admin123";

        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION["admin"] = $email;
            echo "<script>alert('✅ Admin Login Successful!'); window.location.href='admin_dashboard.php';</script>";
            exit();
        } else {
            // ✅ Check User Database
            $query = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user["password"])) {
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_type"] = $user["user_type"];
                    $_SESSION["name"] = $user["name"];
                    echo "<script>alert('✅ Login Successful!'); window.location.href='dashboard.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('❌ Invalid password!');</script>";
                }
            } else {
                echo "<script>alert('⚠️ Email not found! Please register.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Complaint & Feedback System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #1a2a6c, #b21f1f, #fdbb2d);
            color: white;
        }

        /* Login Box */
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
            animation: slideIn 0.8s ease-in-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        h2 {
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: bold;
        }

        /* Input Fields */
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        /* Login Button */
        button {
            background: #ff9800;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #e67e22;
        }

        /* Register Link */
        p {
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #ff9800;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>🔐 User Login</h2>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="📧 Enter your email" required>
            <input type="password" name="password" placeholder="🔑 Enter your password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>

        <!-- Social Media Icons -->
        <div class="social-icons" style="margin-top: 15px;">
            <a href="#"><i class="fab fa-facebook-f" style="color: white;"></i></a>
            <a href="#"><i class="fab fa-twitter" style="color: white;"></i></a>
            <a href="#"><i class="fab fa-instagram" style="color: white;"></i></a>
            <a href="#"><i class="fab fa-youtube" style="color: white;"></i></a>
            <a href="#"><i class="fab fa-whatsapp" style="color: white;"></i></a>
        </div>
    </div>

</body>
</html>


