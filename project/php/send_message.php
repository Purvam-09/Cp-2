<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Get form data
    $receiver_id = intval($_POST['receiver_id']);
    $message = sanitize($conn, $_POST['message']);
    
    // Insert message
    $query = "INSERT INTO messages (sender_id, receiver_id, message, is_read, created_at)
              VALUES (?, ?, ?, 0, NOW())";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $receiver_id, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error sending message']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>