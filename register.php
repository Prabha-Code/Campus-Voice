<?php
session_start();
include "db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Secure password hashing
    $userType = $_POST["userType"]; // student or teacher

    // Check if email already exists
    $checkQuery = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('❌ Email already exists! Try logging in.'); window.location.href='login.php';</script>";
    } else {
        // Insert user into database
        $query = "INSERT INTO users (name, email, password, user_type) VALUES ('$name', '$email', '$password', '$userType')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('✅ Registration Successful! Please login.'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Complaint & Feedback System</title>
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #8360c3, #2ebf91);
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #2ebf91;
            outline: none;
            box-shadow: 0px 0px 5px rgba(46, 191, 145, 0.5);
        }

        .error-message {
            color: red;
            font-size: 0.8em;
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #2ebf91;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #1f9b76;
            transform: scale(1.05);
        }

        p {
            margin-top: 10px;
            font-size: 0.9em;
        }

        p a {
            color: #2ebf91;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 Register</h2>
        <form action="" method="POST" id="registerForm">
            <div class="input-group">
                <input type="text" name="name" id="name" placeholder="Enter Full Name" required>
                <span class="error-message" id="nameError"></span>
            </div>
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder="Enter College Email" required>
                <span class="error-message" id="emailError"></span>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <span class="error-message" id="passwordError"></span>
            </div>
            
            <div class="input-group">
                <select name="userType" id="userType">
                    <option value="student">Student 🎓</option>
                    <option value="teacher">Teacher 👨‍🏫</option>
                </select>
            </div>
            <button type="submit" id="registerBtn">🚀 Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        document.getElementById("registerForm").addEventListener("submit", function (e) {
            let name = document.getElementById("name").value.trim();
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let isValid = true;

            // Name Validation
            if (name.length < 3) {
                document.getElementById("nameError").innerText = "⚠️ Name must be at least 3 characters";
                isValid = false;
            } else {
                document.getElementById("nameError").innerText = "";
            }

            // Email Validation
            let emailPattern = /^[a-zA-Z0-9._%+-]+@citchennai\.net$/;
            if (!emailPattern.test(email)) {
                document.getElementById("emailError").innerText = "⚠️ Use a valid CIT Chennai email (example@citchennai.net)";
                isValid = false;
            } else {
                document.getElementById("emailError").innerText = "";
            }

            // Password Validation
            if (password.length < 6) {
                document.getElementById("passwordError").innerText = "⚠️ Password must be at least 6 characters";
                isValid = false;
            } else {
                document.getElementById("passwordError").innerText = "";
            }

            if (!isValid) {
                e.preventDefault(); // Stop form submission
            }
        });
    </script>
</body>
</html>
