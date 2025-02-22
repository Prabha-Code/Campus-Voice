<?php
session_start();
include "db.php"; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_complaint"])) {
    $complaint_id = "CIT" . rand(1000, 9999); // Generate unique complaint ID
    
    if (isset($_SESSION["user_id"])) {
        // Logged-in user complaint submission
        $user_id = $_SESSION["user_id"];
        $category = $_POST["category"];
        $subcategory = $_POST["subcategory"];
        $query_text = mysqli_real_escape_string($conn, $_POST["query"]);
        $image_name = null;
        $image_path = null;

        if (isset($_POST["image_checkbox"]) && $_POST["image_checkbox"] == "yes") {
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                $image_name = $_FILES["image"]["name"];
                $image_tmp = $_FILES["image"]["tmp_name"];
                $target_dir = "uploads/";
                $image_path = $target_dir . basename($image_name);
                move_uploaded_file($image_tmp, $image_path);
            }
        }

        $assigned_email = ($category == "academic") ? "academic@citchennai.net" : "nonacademic@citchennai.net";
        
        $stmt = $conn->prepare("INSERT INTO complaints (complaint_id, user_id, category, subcategory, query_text, assigned_email, status, image_name, image_path) 
                                VALUES (?, ?, ?, ?, ?, ?, 'Under Review', ?, ?)");
        $stmt->bind_param("sissssss", $complaint_id, $user_id, $category, $subcategory, $query_text, $assigned_email, $image_name, $image_path);
    } else {
        // General complaint submission
        $name = $_POST['name'];
        $email = $_POST['email'];
        $category = $_POST['category'];
        $department = $_POST['department'];
        $description = $_POST['description'];
        
        $stmt = $conn->prepare("INSERT INTO complaints (complaint_id, name, email, category, department, query_text) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $complaint_id, $name, $email, $category, $department, $description);
    }

    if ($stmt->execute()) {
        $subject = "New Complaint Submitted - ID: $complaint_id";
        $message = "A new complaint has been submitted.\n\nComplaint ID: $complaint_id\nCategory: $category\nComplaint: $query_text";
        $headers = "From: no-reply@yourwebsite.com";
        
        // Send email to the assigned department
        mail($assigned_email, $subject, $message, $headers);
        
        echo "<script>
                alert('✅ Complaint Submitted Successfully! Your Complaint ID: $complaint_id');
                window.location.href='dashboard.php';
              </script>";
    } else {
        echo "<script>alert('❌ Error submitting complaint. Please try again.');</script>";
    }
}

// Auto-Escalation Logic
function escalateComplaints() {
    global $conn;
    
    $query = "SELECT id, email, query_text FROM complaints WHERE status = 'Under Review' AND TIMESTAMPDIFF(DAY, created_at, NOW()) >= 3";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $complaintId = $row["id"];
            $userEmail = $row["email"];
            $complaintText = $row["query_text"];

            $to = "redressal@yourdomain.com";
            $subject = "Escalated Complaint ID: $complaintId";
            $message = "Complaint from $userEmail has not been resolved for 3 days.\n\nComplaint: $complaintText";
            $headers = "From: no-reply@yourwebsite.com";

            if (mail($to, $subject, $message, $headers)) {
                $updateQuery = "UPDATE complaints SET status = 'Escalated' WHERE id = $complaintId";
                $conn->query($updateQuery);
            }
        }
    }
}

// Uncomment below line to run escalation logic manually or schedule as a cron job
// escalateComplaints();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Management System</title>
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
        select, textarea, input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Complaint</h2>

        <form method="POST" enctype="multipart/form-data">
            <?php if (isset($_SESSION["user_id"])): ?>
                <!-- Logged-in User Complaint Form -->
                <label>Category:</label>
                <select name="category" id="category" required onchange="updateSubcategories()">
                    <option value="academic">Academic</option>
                    <option value="non-academic">Non-Academic</option>
                </select>
                
                <label>Subcategory:</label>
                <select name="subcategory" id="subcategory" required></select>
                
                <textarea name="query" placeholder="Describe your complaint..." required></textarea>
                
                <label>
                    <input type="checkbox" name="image_checkbox" id="image_checkbox" value="yes"> Check to
                </label>
                
                <label>Upload File (Optional):</label>
                <input type="file" name="image" id="image" disabled>
                
            <?php else: ?>
                <!-- General Complaint Form -->
                <label>Name:</label>
                <input type="text" name="name" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Category:</label>
                <input type="text" name="category" required>

                <label>Department (e.g., CSE, AIDS, ECE):</label>
                <input type="text" name="department" required>

                <label>Description:</label>
                <textarea name="description" required></textarea>
            <?php endif; ?>

            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>
    </div>

    <script>
        // Enable/Disable the file input based on checkbox
        document.getElementById('image_checkbox')?.addEventListener('change', function() {
            var imageInput = document.getElementById('image');
            if (this.checked) {
                imageInput.disabled = false;
            } else {
                imageInput.disabled = true;
            }
        });

        function updateSubcategories() {
            var category = document.getElementById("category").value;
            var subcategory = document.getElementById("subcategory");
            subcategory.innerHTML = "";

            var academicOptions = ["Lab", "Classroom", "Campus", "Infrastructure", "Faculty", "Sanitation", "Others"];
            var nonAcademicOptions = ["Mess", "Hostel", "Laundry", "Playground", "Gym","Transport" ,"Others"];
            var options = (category === "academic") ? academicOptions : nonAcademicOptions;

            options.forEach(function(option) {
                var opt = document.createElement("option");
                opt.value = option.toLowerCase();
                opt.textContent = option;
                subcategory.appendChild(opt);
            });
        }
    </script>
</body>
</html>

