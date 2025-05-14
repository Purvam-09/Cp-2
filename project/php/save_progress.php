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
    $tracking_date = sanitize($conn, $_POST['date']);
    $weight = sanitize($conn, $_POST['weight']);
    $body_fat = isset($_POST['body_fat']) ? sanitize($conn, $_POST['body_fat']) : null;
    $chest = isset($_POST['chest']) ? sanitize($conn, $_POST['chest']) : null;
    $waist = isset($_POST['waist']) ? sanitize($conn, $_POST['waist']) : null;
    $hips = isset($_POST['hips']) ? sanitize($conn, $_POST['hips']) : null;
    $notes = isset($_POST['notes']) ? sanitize($conn, $_POST['notes']) : null;
    
    // Insert progress data
    $query = "INSERT INTO progress_tracking (
                user_id, tracking_date, weight, body_fat_percentage,
                chest_cm, waist_cm, hips_cm, notes, created_at
              ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, NOW()
              )";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt, 
        "isddddds",
        $user_id, $tracking_date, $weight, $body_fat,
        $chest, $waist, $hips, $notes
    );
    
    if (mysqli_stmt_execute($stmt)) {
        // Log activity
        logActivity($conn, $user_id, 'Added progress entry');
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error saving progress']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>