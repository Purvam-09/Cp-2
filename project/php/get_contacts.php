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

// Get contacts (nutritionist and admin)
$query = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.profile_image, u.role,
          (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
          FROM users u
          LEFT JOIN client_nutritionist cn ON u.id = cn.nutritionist_id
          WHERE (u.role IN ('nutritionist', 'admin') AND (cn.user_id = ? OR u.role = 'admin'))
          OR u.id IN (
              SELECT sender_id FROM messages WHERE receiver_id = ?
              UNION
              SELECT receiver_id FROM messages WHERE sender_id = ?
          )";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iiii", $user_id, $user_id, $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$contacts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $contacts[] = $row;
}

// Return contacts as JSON
header('Content-Type: application/json');
echo json_encode($contacts);
?>