<?php
// Database configuration
$host = "localhost";
$database = "nutrilife";
$username = "root";
$password = "";

// Create database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Session configuration
session_start();

// Site settings
$site_name = "NutriLife";
$site_email = "info@nutrilife.com";
$admin_email = "admin@nutrilife.com";

// Function to sanitize user input
function sanitize($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Function to check if user is nutritionist
function isNutritionist() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'nutritionist';
}

// Function to get user data
function getUserData($conn, $user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Function to log activity
function logActivity($conn, $user_id, $activity) {
    $query = "INSERT INTO activity_log (user_id, activity, created_at) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $activity);
    mysqli_stmt_execute($stmt);
}
?>