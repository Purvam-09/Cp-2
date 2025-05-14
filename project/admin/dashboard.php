<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.html');
    exit();
}

// Get admin info
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['user_name'];

// Get total users count
$users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$users_result = mysqli_query($conn, $users_query);
$users_count = mysqli_fetch_assoc($users_result)['total'];

// Get total nutritionists count
$nutritionists_query = "SELECT COUNT(*) as total FROM users WHERE role = 'nutritionist'";
$nutritionists_result = mysqli_query($conn, $nutritionists_query);
$nutritionists_count = mysqli_fetch_assoc($nutritionists_result)['total'];

// Get total supplements count
$supplements_query = "SELECT COUNT(*) as total FROM supplements";
$supplements_result = mysqli_query($conn, $supplements_query);
$supplements_count = mysqli_fetch_assoc($supplements_result)['total'];

// Get recent activities
$activities_query = "SELECT a.*, u.first_name, u.last_name 
                    FROM activity_log a 
                    JOIN users u ON a.user_id = u.id 
                    ORDER BY a.created_at DESC 
                    LIMIT 10";
$activities_result = mysqli_query($conn, $activities_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HealthMantra</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <h1><i class="fas fa-leaf"></i> HealthMantra</h1>
                </div>
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="user-profile">
                <div class="user-avatar">
                    <img src="../assets/admin-avatar.jpg" alt="Admin">
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <p>Administrator</p>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="nutritionists.php">
                            <i class="fas fa-user-md"></i>
                            <span>Nutritionists</span>
                        </a>
                    </li>
                    <li>
                        <a href="supplements.php">
                            <i class="fas fa-pills"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li>
                        <a href="subscriptions.php">
                            <i class="fas fa-credit-card"></i>
                            <span>Subscriptions</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../php/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <main class="content">
            <header class="content-header">
                <h1>Admin Dashboard</h1>
                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="notifications">
                        <button class="notifications-btn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="content-body">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $users_count; ?></h3>
                            <p class="stat-label">Total Users</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $nutritionists_count; ?></h3>
                            <p class="stat-label">Nutritionists</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $supplements_count; ?></h3>
                            <p class="stat-label">Supplements</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">$12,450</h3>
                            <p class="stat-label">Monthly Revenue</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Activities</h3>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <?php while ($activity = mysqli_fetch_assoc($activities_result)): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name'] . ' ' . $activity['activity']); ?></p>
                                        <span class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>User Statistics</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="userStats"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Popular Supplements</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="supplementStats"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>