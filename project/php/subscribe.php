<?php
// Include configuration file
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email from form
    $email = sanitize($conn, $_POST['email']);
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        // Redirect back to the page they came from
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Check if email already exists in newsletter subscriptions
    $check_query = "SELECT * FROM newsletter_subscriptions WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Email already subscribed
        $_SESSION['notification'] = "Email is already subscribed to our newsletter!";
    } else {
        // Add email to newsletter subscriptions
        $insert_query = "INSERT INTO newsletter_subscriptions (email, created_at) VALUES (?, NOW())";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "s", $email);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $_SESSION['success'] = "Thank you for subscribing to our newsletter!";
            
            // In a production environment, we would send a welcome email here
        } else {
            $_SESSION['error'] = "Error subscribing to newsletter. Please try again.";
        }
    }
    
    // Redirect back to the page they came from
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    // Not a POST request, redirect to home page
    redirect("../index.html");
}
?>