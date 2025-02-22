<?php
session_start();
include "db.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$username = ($row = $result->fetch_assoc()) ? htmlspecialchars($row['name']) : "User";
$stmt->close();

// Fetch complaint counts
$sql = "SELECT status, COUNT(*) as count FROM complaints WHERE user_id = ? GROUP BY status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize counts
$counts = ['Pending' => 0, 'Under Process' => 0, 'Resolved' => 0];

while ($row = $result->fetch_assoc()) {
    $counts[$row['status']] = $row['count'];
}
$stmt->close();

// Fetch user complaints sorted by created_at DESC
$stmt = $conn->prepare("SELECT complaint_id, subcategory, query_text, status, created_at, resolved_at, resolved_image FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(to right, rgb(56, 44, 164), rgb(27, 77, 117)); 
            color: #fff; 
            text-align: center; 
            padding: 20px; 
        }
        .dashboard { 
            display: flex; 
            justify-content: center; 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .card { 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px; /* Space between icon and text */
            padding: 20px; 
            border-radius: 10px; 
            width: 250px; 
            color: white; 
            font-size: 20px; 
            font-weight: bold;
            text-align: center;
        }
        .pending { background-color: orange; }
        .underprocess { background-color: blue; }
        .resolved { background-color: green; }
        .card i { font-size: 24px; }

        table { 
            width: 100%; 
            margin: auto; 
            background: rgba(13, 13, 52, 0.15); 
            border-collapse: collapse; 
            border-radius: 10px; 
            overflow: hidden; 
            backdrop-filter: blur(10px); 
            color: white; 
        }
        th, td { 
            padding: 12px; 
            border-bottom: 1px solid rgba(154, 22, 22, 0.3); 
            text-align: center; 
        }
        th { background: rgba(255, 255, 255, 0.3); }
        tr:hover { background: rgba(255, 255, 255, 0.2); transition: 0.3s; }
        .status { 
            font-weight: bold; 
            padding: 8px; 
            border-radius: 5px; 
            display: inline-block; 
        }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 12px 20px; 
            font-size: 16px; 
            font-weight: bold; 
            text-decoration: none; 
            border-radius: 5px; 
            transition: 0.3s; 
            border: none; 
            cursor: pointer; 
            margin-top: 20px; 
        }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>📋 <?php echo $username; ?>'s Complaint Dashboard</h1>

    <div class="dashboard">
        <div class="card pending">
            <i class="fas fa-exclamation-circle"></i> 
            Pending: <?php echo $counts['Pending']; ?>
        </div>
        
        <div class="card underprocess">
            <i class="fas fa-compass fa-spin"></i> 
            Under Process: <?php echo $counts['In Progress']; ?>
        </div>
        
        <div class="card resolved">
            <i class="fas fa-check-circle"></i> 
            Resolved: <?php echo $counts['Resolved']; ?>
        </div>
    </div>

    <table border="0">
        <tr>
            <th>Complaint ID</th>
            <th>Category</th>
            <th>Query</th>
            <th>Complaint Date</th>
            <th>Status</th>
            <th>Resolved Date</th>
            <th>Resolved Image</th>
            <th>Escalate</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { 
            $created_at = strtotime($row['created_at']);
            $current_time = time();
            $time_diff = ($current_time - $created_at) / 60;
        ?>
            <tr>
                <td>#<?php echo $row['complaint_id']; ?></td>
                <td><?php echo htmlspecialchars($row['subcategory']); ?></td>
                <td><?php echo htmlspecialchars($row['query_text']); ?></td>
                <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                <td><span class="status <?php echo strtolower(str_replace(' ', '', $row['status'])); ?>"> <?php echo $row['status']; ?> </span></td>
                <td><?php echo ($row['resolved_at']) ? date("d M Y", strtotime($row['resolved_at'])) : "N/A"; ?></td>
                <td>
                    <?php if (!empty($row['resolved_image']) && file_exists('uploads/' . htmlspecialchars($row['resolved_image']))) { ?>
                        <a href="uploads/<?php echo htmlspecialchars($row['resolved_image']); ?>" target="_blank">
                            <img src="uploads/<?php echo htmlspecialchars($row['resolved_image']); ?>" alt="Resolved Image" width="100">
                        </a>
                    <?php } else { echo "N/A"; } ?>
                </td>
                <td>
                    <?php if ($row['status'] == 'Pending' && $time_diff > 5) { ?>
                        <a href="send_mail.php?id=<?php echo $row['complaint_id']; ?>" class="btn escalate">Send Mail</a>
                    <?php } else { echo "-"; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <button class="btn" onclick="window.location.href='dashboard.php'">🏠 Return to Dashboard</button>
</body>
</html>
