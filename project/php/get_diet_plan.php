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

// Get nutrition plan
$query = "SELECT np.*, mp.*
          FROM nutrition_plans np
          LEFT JOIN meal_plans mp ON np.id = mp.nutrition_plan_id
          WHERE np.user_id = ?
          ORDER BY np.created_at DESC
          LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$plan = mysqli_fetch_assoc($result);

if ($plan) {
    // Get meal plans for each day
    $meal_plans = [];
    $meal_query = "SELECT * FROM meal_plans WHERE nutrition_plan_id = ? ORDER BY day_of_week, meal_type";
    $meal_stmt = mysqli_prepare($conn, $meal_query);
    mysqli_stmt_bind_param($meal_stmt, "i", $plan['id']);
    mysqli_stmt_execute($meal_stmt);
    $meal_result = mysqli_stmt_get_result($meal_stmt);
    
    while ($meal = mysqli_fetch_assoc($meal_result)) {
        $meal_plans[] = $meal;
    }
    
    $plan['meal_plan'] = $meal_plans;
    
    // Return plan data as JSON
    header('Content-Type: application/json');
    echo json_encode($plan);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No diet plan found']);
}
?>