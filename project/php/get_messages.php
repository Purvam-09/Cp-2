<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get contact ID from query string
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

if ($contact_id === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Contact ID required']);
    exit();
}

// Get messages
$query = "SELECT m.*, 
          u_sender.first_name as sender_first_name, u_sender.last_name as sender_last_name,
          u_receiver.first_name as receiver_first_name, u_receiver.last_name as receiver_last_name
          FROM messages m
          JOIN users u_sender ON m.sender_id = u_sender.id
          JOIN users u_receiver ON m.receiver_id = u_receiver.id
          WHERE (m.sender_id = ? AND m.receiver_id = ?)
          OR (m.sender_id = ? AND m.receiver_id = ?)
          ORDER BY m.created_at ASC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iiii", $user_id, $contact_id, $contact_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

// Mark messages as read
$update_query = "UPDATE messages SET is_read = 1 
                WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
$update_stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($update_stmt, "ii", $user_id, $contact_id);
mysqli_stmt_execute($update_stmt);

// Return messages as JSON
header('Content-Type: application/json');
echo json_encode($messages);
?>