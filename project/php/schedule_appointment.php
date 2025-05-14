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
    $date = sanitize($conn, $_POST['date']);
    $time = sanitize($conn, $_POST['time']);
    $type = sanitize($conn, $_POST['type']);
    $notes = isset($_POST['notes']) ? sanitize($conn, $_POST['notes']) : null;
    
    // Calculate end time (30 minutes after start time)
    $end_time = date('H:i:s', strtotime($time . ' +30 minutes'));
    
    // Get assigned nutritionist
    $nutritionist_query = "SELECT nutritionist_id FROM client_nutritionist WHERE user_id = ?";
    $nutritionist_stmt = mysqli_prepare($conn, $nutritionist_query);
    mysqli_stmt_bind_param($nutritionist_stmt, "i", $user_id);
    mysqli_stmt_execute($nutritionist_stmt);
    $nutritionist_result = mysqli_stmt_get_result($nutritionist_stmt);
    $nutritionist = mysqli_fetch_assoc($nutritionist_result);
    
    if (!$nutritionist) {
        http_response_code(400);
        echo json_encode(['error' => 'No nutritionist assigned']);
        exit();
    }
    
    // Insert appointment
    $query = "INSERT INTO appointments (
                user_id, nutritionist_id, appointment_date, start_time,
                end_time, status, notes, created_at, updated_at
              ) VALUES (
                ?, ?, ?, ?,
                ?, 'scheduled', ?, NOW(), NOW()
              )";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt, 
        "iissss",
        $user_id, $nutritionist['nutritionist_id'], $date, $time,
        $end_time, $notes
    );
    
    if (mysqli_stmt_execute($stmt)) {
        // Log activity
        logActivity($conn, $user_id, 'Scheduled appointment');
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error scheduling appointment']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>