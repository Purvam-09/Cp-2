<?php
// Include configuration file
require_once 'config.php';

// Check if user is logged in
if (isLoggedIn()) {
    // Log activity
    logActivity($conn, $_SESSION['user_id'], 'User logged out');
    
    // Remove remember token if exists
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Delete token from database
        $query = "DELETE FROM remember_tokens WHERE token = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        
        // Delete cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Destroy session
    session_unset();
    session_destroy();
}

// Redirect to home page
redirect("../index.html");
?>