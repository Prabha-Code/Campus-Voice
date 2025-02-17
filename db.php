<?php
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Change to your MySQL username
$password = ""; // Change if your MySQL has a password
$dbname = "complaint_system"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
