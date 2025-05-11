<?php
// Include configuration file
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = sanitize($conn, $_POST['first_name']);
    $last_name = sanitize($conn, $_POST['last_name']);
    $email = sanitize($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        redirect("../pages/signup.html");
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        redirect("../pages/signup.html");
        exit();
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        redirect("../pages/signup.html");
        exit();
    }
    
    // Check password strength
    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long.";
        redirect("../pages/signup.html");
        exit();
    }
    
    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email or login.";
        redirect("../pages/signup.html");
        exit();
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate verification token
    $verification_token = generateRandomString(32);
    
    // Insert user into database
    $query = "INSERT INTO users (first_name, last_name, email, password, role, status, verification_token, created_at) 
              VALUES (?, ?, ?, ?, 'user', 'active', ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $password_hash, $verification_token);
    
    if (mysqli_stmt_execute($stmt)) {
        // Get the newly inserted user ID
        $user_id = mysqli_insert_id($conn);
        
        // Create session for the user
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'user';
        
        // Log activity
        logActivity($conn, $user_id, 'User registered');
        
        // Send verification email (in production)
        // For demo, we'll just redirect to the health profile page
        redirect("../pages/health_profile.html");
    } else {
        $_SESSION['error'] = "Error creating account. Please try again.";
        redirect("../pages/signup.html");
    }
} else {
    // Not a POST request, redirect to signup page
    redirect("../pages/signup.html");
}
?>