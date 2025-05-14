<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is nutritionist
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'nutritionist') {
    header('Location: ../pages/login.html');
    exit();
}

// Get nutritionist info
$nutritionist_id = $_SESSION['user_id'];
$nutritionist_name = $_SESSION['user_name'];

// Get total clients count
$clients_query = "SELECT COUNT(*) as total FROM client_nutritionist WHERE nutritionist_id = ?";
$clients_stmt = mysqli_prepare($conn, $clients_query);
mysqli_stmt_bind_param($clients_stmt, "i", $nutritionist_id);
mysqli_stmt_execute($clients_stmt);
$clients_result = mysqli_stmt_get_result($clients_stmt);
$clients_count = mysqli_fetch_assoc($clients_result)['total'];

// Get upcoming appointments
$appointments_query = "SELECT a.*, u.first_name, u.last_name 
                      FROM appointments a 
                      JOIN users u ON a.user_id = u.id 
                      WHERE a.nutritionist_id = ? 
                      AND a.status = 'scheduled' 
                      AND a.appointment_date >= CURDATE() 
                      ORDER BY a.appointment_date, a.start_time 
                      LIMIT 5";
$appointments_stmt = mysqli_prepare($conn, $appointments_query);
mysqli_stmt_bind_param($appointments_stmt, "i", $nutritionist_id);
mysqli_stmt_execute($appointments_stmt);
$appointments_result = mysqli_stmt_get_result($appointments_stmt);

// Get unread messages count
$messages_query = "SELECT COUNT(*) as total FROM messages 
                  WHERE receiver_id = ? AND is_read = 0";
$messages_stmt = mysqli_prepare($conn, $messages_query);
mysqli_stmt_bind_param($messages_stmt, "i", $nutritionist_id);
mysqli_stmt_execute($messages_stmt);
$messages_result = mysqli_stmt_get_result($messages_stmt);
$unread_messages = mysqli_fetch_assoc($messages_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutritionist Dashboard - HealthMantra</title>
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
                    <img src="../assets/nutritionist-avatar.jpg" alt="Nutritionist">
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($nutritionist_name); ?></h3>
                    <p>Nutritionist</p>
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
                        <a href="clients.php">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                            <span class="badge"><?php echo $clients_count; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="appointments.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Appointments</span>
                        </a>
                    </li>
                    <li>
                        <a href="diet-plans.php">
                            <i class="fas fa-utensils"></i>
                            <span>Diet Plans</span>
                        </a>
                    </li>
                    <li>
                        <a href="messages.php">
                            <i class="fas fa-comment-alt"></i>
                            <span>Messages</span>
                            <?php if ($unread_messages > 0): ?>
                            <span class="badge"><?php echo $unread_messages; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="profile.php">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
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
                <h1>Nutritionist Dashboard</h1>
                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search clients...">
                    </div>
                    <div class="notifications">
                        <button class="notifications-btn">
                            <i class="fas fa-bell"></i>
                            <?php if ($unread_messages > 0): ?>
                            <span class="notification-badge"><?php echo $unread_messages; ?></span>
                            <?php endif; ?>
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
                            <h3 class="stat-value"><?php echo $clients_count; ?></h3>
                            <p class="stat-label">Total Clients</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">
                                <?php echo mysqli_num_rows($appointments_result); ?>
                            </h3>
                            <p class="stat-label">Upcoming Appointments</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">15</h3>
                            <p class="stat-label">Active Diet Plans</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">4.8</h3>
                            <p class="stat-label">Average Rating</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3>Upcoming Appointments</h3>
                            <div class="card-actions">
                                <a href="appointments.php" class="btn-text">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                            <div class="appointment-item">
                                <div class="appointment-date">
                                    <div class="date-box">
                                        <span class="month"><?php echo date('M', strtotime($appointment['appointment_date'])); ?></span>
                                        <span class="day"><?php echo date('d', strtotime($appointment['appointment_date'])); ?></span>
                                    </div>
                                </div>
                                <div class="appointment-details">
                                    <h4><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h4>
                                    <div class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        <?php echo date('g:i A', strtotime($appointment['start_time'])); ?> - 
                                        <?php echo date('g:i A', strtotime($appointment['end_time'])); ?>
                                    </div>
                                </div>
                                <div class="appointment-actions">
                                    <button class="btn btn-outline btn-sm">View Details</button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Client Progress</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="clientProgress"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Messages</h3>
                            <div class="card-actions">
                                <a href="messages.php" class="btn-text">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Messages will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/nutritionist.js"></script>
</body>
</html>