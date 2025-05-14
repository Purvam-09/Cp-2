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

// Get progress data
$query = "SELECT * FROM progress_tracking 
          WHERE user_id = ? 
          ORDER BY tracking_date DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$progress_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $progress_data[] = $row;
}

// Return progress data as JSON
header('Content-Type: application/json');
echo json_encode($progress_data);
?>