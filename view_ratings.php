<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "complaint_system"; // Change this to your actual database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define categories and subcategories
$categories = [
    'academic' => ['Lab', 'Classroom', 'Campus', 'Faculty', 'Sanitation', 'Infrastructure', 'Others'],
    'non-academic' => ['Mess', 'Hostel', 'Laundry', 'Playground', 'Gym', 'Transport', 'Others']
];

// Image URLs for WhatsApp-style icons
$imageUrls = [
    "Lab" => "https://thumbs.dreamstime.com/b/laboratory-glassware-color-background-51048296.jpg",
    "Classroom" => "https://thumbs.dreamstime.com/b/classroom-kids-teacher-professor-teaches-students-first-grade-elementary-school-class-little-children-preschool-120236345.jpg",
    "Campus" => "https://images.shiksha.com/mediadata/images/1706768742phpiVO69u.jpeg",
    "Faculty" => "https://media.istockphoto.com/id/1366724877/photo/rear-view-of-mature-teacher-talking-to-his-student-during-lecture-at-university-classroom.jpg?s=612x612&w=0&k=20&c=PYpAFHxiUKX2i1D8w_ElnGsm1B64GBleyF-DfYTLcN0=",
    "Sanitation" => "https://static.vecteezy.com/system/resources/previews/009/741/978/non_2x/color-sanitation-elements-icons-set-vector.jpg",
    "Infrastructure" => "https://www.shutterstock.com/image-vector/school-learning-concept-3d-isometric-600nw-2290010703.jpg",
    "Others" => "https://www.gilaposter.com/image/gilaposter/image/data/Accurator%20Others%20Logo.png",
    "Mess" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTE8tvQaTfxiQbS6tFAMMskqRiWHqZ3gvnNoQ&s",
    "Hostel" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4DuucKUwcylyDVa4exHHON6YxGsoAzBskEQ&s",
    "Laundry" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmZCoOHpsicC-z63B4hibCzlU6YZfMoCwzhg&s",
    "Playground" => "https://www.shutterstock.com/image-photo/school-yarda-football-field-building-600nw-2396312857.jpg",
    "Gym" => "https://media.istockphoto.com/id/1322158059/photo/dumbbell-water-bottle-towel-on-the-bench-in-the-gym.jpg?s=612x612&w=0&k=20&c=CIdh6LPGwU6U6lbvKCdd7LcppidaYwcDawXJI-b0yGE=",
    "Transport" => "https://nmcauditingcollege.com/wp-content/uploads/2019/03/transport-e1554783292276.png"
];

// Function to calculate dynamic ratings
function getDynamicRating($conn, $subcategory) {
    $defaultRating = 4.5; // Default base rating

    // Prepared statement to prevent SQL injection
    $stmtPending = $conn->prepare("SELECT COUNT(*) AS total FROM complaints WHERE subcategory = ? AND (status = 'Under Review' OR status = 'Pending')");
    $stmtPending->bind_param("s", $subcategory);
    $stmtPending->execute();
    $pendingCount = ($stmtPending->get_result()->fetch_assoc())['total'] ?? 0;
    
    $stmtResolved = $conn->prepare("SELECT COUNT(*) AS total FROM complaints WHERE subcategory = ? AND status = 'Resolved'");
    $stmtResolved->bind_param("s", $subcategory);
    $stmtResolved->execute();
    $resolvedCount = ($stmtResolved->get_result()->fetch_assoc())['total'] ?? 0;

    $stmtPending->close();
    $stmtResolved->close();

    // Adjust ratings: Pending complaints reduce rating, resolved increase rating
    $rating = max(1, min(5, $defaultRating - ($pendingCount * 0.1) + ($resolvedCount * 0.05)));

    return round($rating, 1);
}

// Function to display star ratings
function displayStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);
    
    return str_repeat('⭐', $fullStars) . ($halfStar ? '⭐' : '') . str_repeat('☆', $emptyStars);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings and Complaints</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(164, 94, 190);
            margin: 20px;
            text-align: center;
        }
        .category-container {
            margin-bottom: 30px;
            padding: 15px;
            background: rgb(80, 190, 212);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .subcategory-box {
            display: inline-block;
            width: 120px; /* Adjusted for WhatsApp-style layout */
            margin: 10px;
            padding: 10px;
            background: rgb(78, 75, 222);
            color: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .subcategory-box img {
            width: 60px; /* WhatsApp-style icon size */
            height: 60px;
            border-radius: 50%; /* Circular images */
            object-fit: cover; /* Ensures proper cropping */
            margin-bottom: 5px;
            border: 2px solid white; /* White border to match WhatsApp style */
        }
        .stars {
            margin-top: 5px;
            font-size: 14px;
        }
        .rating-text {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<?php
// Display categories and subcategories with dynamic ratings and WhatsApp-style icons
foreach ($categories as $category => $subcategories) {
    echo "<div class='category-container'>";
    echo "<h2>" . ucfirst($category) . " Ratings</h2>";
    foreach ($subcategories as $subcategory) {
        $rating = getDynamicRating($conn, $subcategory);
        $imagePath = $imageUrls[$subcategory] ?? "images/default.png"; // Use direct URL or fallback

        echo "<div class='subcategory-box'>";
        echo "<img src='$imagePath' alt='$subcategory Image' onerror=\"this.onerror=null;this.src='images/default.png';\">";
        echo "<br><strong>$subcategory</strong><br>";
        echo "<br><div class='rating-text'>Rating: $rating</div>"; // Display rating number
        echo "<div class='stars'>" . displayStars($rating) . "</div>";
        echo "</div>";
    }
    echo "</div>";
}

// Close database connection
$conn->close();
?>

</body>
</html>

