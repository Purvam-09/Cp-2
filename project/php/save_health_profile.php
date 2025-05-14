<?php
// Include configuration file
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect("../pages/login.html");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Basic Information
    $birth_date = sanitize($conn, $_POST['birth_date']);
    $gender = sanitize($conn, $_POST['gender']);
    $phone = sanitize($conn, $_POST['phone']);
    $address = sanitize($conn, $_POST['address']);
    
    // Health Data
    $height = sanitize($conn, $_POST['height']);
    $height_unit = sanitize($conn, $_POST['height_unit']);
    $weight = sanitize($conn, $_POST['weight']);
    $weight_unit = sanitize($conn, $_POST['weight_unit']);
    $activity_level = sanitize($conn, $_POST['activity_level']);
    $health_conditions = isset($_POST['health_condition']) ? $_POST['health_condition'] : [];
    $health_conditions_json = json_encode($health_conditions);
    
    // Convert height and weight to standard units (cm and kg)
    if ($height_unit === 'feet') {
        $height = $height * 30.48; // Convert feet to cm
    }
    
    if ($weight_unit === 'lb') {
        $weight = $weight / 2.20462; // Convert lb to kg
    }
    
    // Diet & Goals
    $primary_goal = sanitize($conn, $_POST['primary_goal']);
    $dietary_restrictions = isset($_POST['dietary_restriction']) ? $_POST['dietary_restriction'] : [];
    $dietary_restrictions_json = json_encode($dietary_restrictions);
    $allergies = sanitize($conn, $_POST['allergies']);
    $disliked_foods = sanitize($conn, $_POST['disliked_foods']);
    
    // Plan Selection
    $plan_type = sanitize($conn, $_POST['plan_type']);
    
    // Calculate BMI
    $bmi = $weight / (($height / 100) * ($height / 100));
    
    // Insert health profile into database
    $query = "INSERT INTO health_profiles (
                user_id, birth_date, gender, phone, address, 
                height, weight, bmi, activity_level, health_conditions,
                primary_goal, dietary_restrictions, allergies, disliked_foods,
                plan_type, created_at, updated_at
              ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, NOW(), NOW()
              )";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt, 
        "issssdddssssss", 
        $user_id, $birth_date, $gender, $phone, $address,
        $height, $weight, $bmi, $activity_level, $health_conditions_json,
        $primary_goal, $dietary_restrictions_json, $allergies, $disliked_foods,
        $plan_type
    );
    
    if (mysqli_stmt_execute($stmt)) {
        // Log activity
        logActivity($conn, $user_id, 'Completed health profile');
        
        // Create default nutrition plan based on the profile
        createDefaultNutritionPlan($conn, $user_id, $gender, $weight, $height, $activity_level, $primary_goal);
        
        // If premium or elite plan, assign a nutritionist
        if ($plan_type === 'premium' || $plan_type === 'elite') {
            assignNutritionist($conn, $user_id, $plan_type);
        }
        
        // Add subscription record if premium or elite
        if ($plan_type !== 'basic') {
            $amount = $plan_type === 'premium' ? 49.99 : 99.99;
            addSubscription($conn, $user_id, $plan_type, $amount);
        }
        
        // Redirect to dashboard
        redirect("../pages/dashboard.html");
    } else {
        $_SESSION['error'] = "Error saving health profile. Please try again.";
        redirect("../pages/health_profile.html");
    }
} else {
    // Not a POST request, redirect to health profile page
    redirect("../pages/health_profile.html");
}

// Function to create a default nutrition plan
function createDefaultNutritionPlan($conn, $user_id, $gender, $weight, $height, $activity_level, $primary_goal) {
    // Calculate base calories based on weight, height, gender, and activity level
    $base_calories = calculateBaseCalories($gender, $weight, $height, $activity_level);
    
    // Adjust calories based on primary goal
    switch ($primary_goal) {
        case 'weight_loss':
            $target_calories = $base_calories * 0.8; // 20% deficit
            $carb_percentage = 40;
            $protein_percentage = 35;
            $fat_percentage = 25;
            break;
        case 'muscle_gain':
            $target_calories = $base_calories * 1.1; // 10% surplus
            $carb_percentage = 45;
            $protein_percentage = 30;
            $fat_percentage = 25;
            break;
        case 'performance':
            $target_calories = $base_calories * 1.05; // 5% surplus
            $carb_percentage = 50;
            $protein_percentage = 30;
            $fat_percentage = 20;
            break;
        default: // general_health
            $target_calories = $base_calories;
            $carb_percentage = 45;
            $protein_percentage = 25;
            $fat_percentage = 30;
            break;
    }
    
    // Calculate macros in grams
    $protein_grams = ($target_calories * ($protein_percentage / 100)) / 4; // 4 calories per gram of protein
    $carb_grams = ($target_calories * ($carb_percentage / 100)) / 4; // 4 calories per gram of carbs
    $fat_grams = ($target_calories * ($fat_percentage / 100)) / 9; // 9 calories per gram of fat
    
    // Round to nearest whole number
    $target_calories = round($target_calories);
    $protein_grams = round($protein_grams);
    $carb_grams = round($carb_grams);
    $fat_grams = round($fat_grams);
    
    // Create nutrition plan in database
    $query = "INSERT INTO nutrition_plans (
                user_id, target_calories, protein_grams, carb_grams, fat_grams,
                notes, created_by, created_at, updated_at
              ) VALUES (
                ?, ?, ?, ?, ?,
                'Automatically generated based on your profile.', 'system', NOW(), NOW()
              )";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt, 
        "iiiii", 
        $user_id, $target_calories, $protein_grams, $carb_grams, $fat_grams
    );
    
    mysqli_stmt_execute($stmt);
}

// Function to calculate base calories
function calculateBaseCalories($gender, $weight, $height, $activity_level) {
    // Using Mifflin-St Jeor equation
    if ($gender === 'male') {
        $bmr = 10 * $weight + 6.25 * $height - 5 * 30 + 5; // Assuming age 30 for simplicity
    } else {
        $bmr = 10 * $weight + 6.25 * $height - 5 * 30 - 161; // Assuming age 30 for simplicity
    }
    
    // Apply activity multiplier
    switch ($activity_level) {
        case 'sedentary':
            return $bmr * 1.2;
        case 'light':
            return $bmr * 1.375;
        case 'moderate':
            return $bmr * 1.55;
        case 'active':
            return $bmr * 1.725;
        default:
            return $bmr * 1.2;
    }
}

// Function to assign a nutritionist
function assignNutritionist($conn, $user_id, $plan_type) {
    // Get a nutritionist with the fewest clients
    $query = "SELECT u.id, COUNT(c.id) as client_count
              FROM users u
              LEFT JOIN client_nutritionist c ON u.id = c.nutritionist_id
              WHERE u.role = 'nutritionist'
              GROUP BY u.id
              ORDER BY client_count ASC
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $nutritionist_id = $row['id'];
        
        // Create client-nutritionist relationship
        $insert_query = "INSERT INTO client_nutritionist (
                            user_id, nutritionist_id, plan_type, created_at
                         ) VALUES (
                            ?, ?, ?, NOW()
                         )";
        
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $nutritionist_id, $plan_type);
        mysqli_stmt_execute($stmt);
    }
}

// Function to add subscription
function addSubscription($conn, $user_id, $plan_type, $amount) {
    // Create subscription record
    $query = "INSERT INTO subscriptions (
                user_id, plan_type, amount, start_date, end_date, status
              ) VALUES (
                ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 'active'
              )";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isd", $user_id, $plan_type, $amount);
    mysqli_stmt_execute($stmt);
}
?>