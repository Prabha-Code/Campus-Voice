<?php
include "db.php";

$query = "SELECT image_path FROM complaints WHERE complaint_id = 1";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "<img src='" . $row['image_path'] . "' width='200px' height='200px'><br>";
}
?>
