<?php
session_start();
// Include configuration file
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Login attempt received");
    // Get form data
    $email = sanitize($conn, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    error_log("Attempting login for email: " . $email);
    
    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please enter both email and password.";
        redirect("../pages/login.html");
        exit();
    }
    
    // Check if user exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        // User exists, verify password
        $user = mysqli_fetch_assoc($result);
        error_log("User found with role: " . $user['role']);
        
        if (password_verify($password, $user['password'])) {
            error_log("Password verified successfully");
            // Password is correct, create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            error_log("Session variables set: " . print_r($_SESSION, true));
            
            // Update last login time
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
            mysqli_stmt_execute($update_stmt);
            
            // Log activity
            logActivity($conn, $user['id'], 'User logged in');
            
            // If remember me is checked, set cookie
            if ($remember) {
                $token = generateRandomString(32);
                $expire = time() + (30 * 24 * 60 * 60); // 30 days
                
                // Store token in database
                $token_query = "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))";
                $token_stmt = mysqli_prepare($conn, $token_query);
                mysqli_stmt_bind_param($token_stmt, "isi", $user['id'], $token, $expire);
                mysqli_stmt_execute($token_stmt);
                
                // Set cookie
                setcookie('remember_token', $token, $expire, '/', '', true, true);
            }
            
            // Debug: Log the user role to help with troubleshooting
            error_log("User role: " . $user['role']);
            
            // Redirect based on user role
            if ($user['role'] === 'admin') {
                error_log("Redirecting to admin dashboard");
                redirect("../admin/dashboard.php");
            } elseif ($user['role'] === 'nutritionist') {
                error_log("Redirecting to nutritionist dashboard");
                redirect("../try%20project/project/nutritionist/dashboard.php");
                exit();
            } else {
                error_log("Redirecting to user dashboard");
                // Redirect regular user to appropriate dashboard
                // Check if health profile is completed
                $profile_query = "SELECT * FROM health_profiles WHERE user_id = ?";
                $profile_stmt = mysqli_prepare($conn, $profile_query);
                mysqli_stmt_bind_param($profile_stmt, "i", $user['id']);
                mysqli_stmt_execute($profile_stmt);
                $profile_result = mysqli_stmt_get_result($profile_stmt);
                
                if (mysqli_num_rows($profile_result) == 0) {
                    // Health profile not completed
                    redirect("../pages/health_profile.html");
                } else {
                    // Health profile completed
                    redirect("../pages/dashboard.html");
                }
            }
        } else {
            error_log("Password verification failed");
            // Incorrect password
            $_SESSION['error'] = "Incorrect password. Please try again.";
            redirect("../pages/login.html");
        }
    } else {
        error_log("No user found with email: " . $email);
        // User does not exist
        $_SESSION['error'] = "No account found with this email. Please sign up.";
        redirect("../pages/login.html");
    }
} else {
    // Not a POST request, redirect to login page
    redirect("../pages/login.html");
}
?>