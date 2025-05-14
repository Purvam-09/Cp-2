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

// Get user data
$query = "SELECT u.*, hp.plan_type 
          FROM users u 
          LEFT JOIN health_profiles hp ON u.id = hp.user_id 
          WHERE u.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    // Remove sensitive information
    unset($user['password']);
    unset($user['verification_token']);
    unset($user['reset_token']);
    unset($user['reset_token_expiry']);
    
    // Return user data as JSON
    header('Content-Type: application/json');
    echo json_encode($user);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>