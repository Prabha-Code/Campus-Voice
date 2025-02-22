<?php
session_start();
$conn = new mysqli("localhost", "root", "", "complaint_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendWhatsAppMessage($number, $message) {
    $access_token = "EAAI7KVz80JoBO4K3CDnpt6DJjWhxGtN1aYEj42xdtvX328yIt9okCthtS12s11Cp0cgRz4pP97DqMW6HCbuhCZA8bYYxdNCOgyKDDFU2wAjF3z4CReQtlCXZASO0Xc2ZCinRKI68Mc0bhKbF5wSnG2uNy2LZBpJD2Ws5a3KBWb9MXuMAJYhZCsm9deNsTAZAZAAWXg97okJKC7rs25QBzPQHJvLGEtUVxCt92ZCiLfZALZClNWovYMd2bQSvAolM9j24rLdi3ckgZDZD";
    $url = "https://graph.facebook.com/v18.0/627998793191578/messages";

    $data = [
        "messaging_product" => "whatsapp",
        "to" => $number,
        "type" => "text",
        "text" => ["body" => $message]
    ];

    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\nAuthorization: Bearer " . $access_token,
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_message = strtolower(trim($_POST['message']));
    
    if (strpos($user_message, "complaint") !== false) {
        if (!isset($_SESSION['user_phone'])) {
            $_SESSION['awaiting_phone'] = true;
            echo json_encode(["reply" => "Please enter your mobile number:"]);
            exit;
        }
        
        $complaint = trim(str_replace("complaint:", "", $user_message));
        $user_phone = $_SESSION['user_phone'];
        $sql = "INSERT INTO complaints (user_id, complaint, status, phone) VALUES (1, '$complaint', 'Pending', '$user_phone')";
        if ($conn->query($sql) === TRUE) {
            $complaint_id = $conn->insert_id;
            $response_message = "Your complaint has been registered. Your complaint ID is " . $complaint_id;
            echo json_encode(["reply" => $response_message]);
            
            // Send WhatsApp notification to admin
            $admin_phone = "+917845386801";
            sendWhatsAppMessage($admin_phone, "New complaint received: $complaint (ID: $complaint_id)");
            
            // Send WhatsApp confirmation to user
            sendWhatsAppMessage($user_phone, "Your complaint (ID: $complaint_id) has been registered and is currently pending.");
        } else {
            echo json_encode(["reply" => "Error in registering complaint"]);
        }
    } elseif (strpos($user_message, "status") !== false) {
        preg_match('/\d+/', $user_message, $matches);
        if (!empty($matches)) {
            $complaint_id = $matches[0];
            $result = $conn->query("SELECT status, phone FROM complaints WHERE id = $complaint_id");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $status_message = "Your complaint status: " . $row['status'];
                echo json_encode(["reply" => $status_message]);
                
                // Send WhatsApp update to user
                sendWhatsAppMessage($row['phone'], "Update: Your complaint (ID: $complaint_id) status is now: " . $row['status']);
            } else {
                echo json_encode(["reply" => "Invalid complaint ID"]);
            }
        } else {
            echo json_encode(["reply" => "Please provide a valid complaint ID"]);
        }
    } elseif (isset($_SESSION['awaiting_phone'])) {
        $_SESSION['user_phone'] = $user_message;
        unset($_SESSION['awaiting_phone']);
        echo json_encode(["reply" => "Thank you! Now you can submit your complaint by typing 'Complaint: [your issue]'."]);
    } else {
        echo json_encode(["reply" => "I can help with complaints. Type 'Complaint: [your issue]' to register a complaint or 'Status [ID]' to check status."]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Chatbot</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #chatbox { width: 300px; height: 400px; border: 1px solid #ccc; overflow-y: scroll; padding: 10px; }
        #user-input { width: 240px; }
        .bot-message { color: blue; }
        .user-message { color: green; }
    </style>
</head>
<body>
    <div id="chatbox"></div>
    <input type="text" id="user-input" placeholder="Type your message...">
    <button onclick="sendMessage()">Send</button>

    <script>
        function sendMessage() {
            var message = $("#user-input").val();
            $("#chatbox").append("<div class='user-message'>You: " + message + "</div>");
            $("#user-input").val('');

            $.post("", { message: message }, function(response) {
                var data = JSON.parse(response);
                $("#chatbox").append("<div class='bot-message'>Bot: " + data.reply + "</div>");
            });
        }
    </script>
</body>
</html>