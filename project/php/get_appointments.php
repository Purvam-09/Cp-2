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

// Get appointments
$query = "SELECT a.*, u.first_name, u.last_name, u.profile_image 
          FROM appointments a 
          JOIN users u ON a.nutritionist_id = u.id 
          WHERE a.user_id = ? 
          ORDER BY a.appointment_date DESC, a.start_time DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

// Return appointments as JSON
header('Content-Type: application/json');
echo json_encode($appointments);
?>