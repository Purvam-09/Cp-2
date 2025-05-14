-- Database: healthmantra

CREATE DATABASE IF NOT EXISTS healthmantra;
USE healthmantra;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'nutritionist', 'admin') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    profile_image VARCHAR(255) DEFAULT NULL,
    verification_token VARCHAR(255) DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL
);

-- Health Profiles Table
CREATE TABLE IF NOT EXISTS health_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    height FLOAT NOT NULL COMMENT 'in cm',
    weight FLOAT NOT NULL COMMENT 'in kg',
    bmi FLOAT NOT NULL,
    activity_level ENUM('sedentary', 'light', 'moderate', 'active') NOT NULL,
    health_conditions JSON DEFAULT NULL,
    primary_goal ENUM('weight_loss', 'muscle_gain', 'general_health', 'performance') NOT NULL,
    dietary_restrictions JSON DEFAULT NULL,
    allergies TEXT DEFAULT NULL,
    disliked_foods TEXT DEFAULT NULL,
    plan_type ENUM('basic', 'premium', 'elite') NOT NULL DEFAULT 'basic',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Nutrition Plans Table
CREATE TABLE IF NOT EXISTS nutrition_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_calories INT NOT NULL,
    protein_grams INT NOT NULL,
    carb_grams INT NOT NULL,
    fat_grams INT NOT NULL,
    notes TEXT DEFAULT NULL,
    created_by VARCHAR(50) NOT NULL COMMENT 'user ID or "system"',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Meal Plans Table
CREATE TABLE IF NOT EXISTS meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nutrition_plan_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack1', 'snack2') NOT NULL,
    meal_name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    calories INT NOT NULL,
    protein_grams INT NOT NULL,
    carb_grams INT NOT NULL,
    fat_grams INT NOT NULL,
    recipe TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (nutrition_plan_id) REFERENCES nutrition_plans(id)
);

-- Progress Tracking Table
CREATE TABLE IF NOT EXISTS progress_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tracking_date DATE NOT NULL,
    weight FLOAT DEFAULT NULL COMMENT 'in kg',
    body_fat_percentage FLOAT DEFAULT NULL,
    chest_cm FLOAT DEFAULT NULL,
    waist_cm FLOAT DEFAULT NULL,
    hips_cm FLOAT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Supplements Table
CREATE TABLE IF NOT EXISTS supplements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('vitamins', 'minerals', 'proteins', 'omega', 'herbs') NOT NULL,
    description TEXT NOT NULL,
    benefits TEXT NOT NULL,
    usage_instructions TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Supplement Recommendations Table
CREATE TABLE IF NOT EXISTS supplement_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    supplement_id INT NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    notes TEXT DEFAULT NULL,
    recommended_by INT NOT NULL COMMENT 'nutritionist ID',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (supplement_id) REFERENCES supplements(id),
    FOREIGN KEY (recommended_by) REFERENCES users(id)
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nutritionist_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (nutritionist_id) REFERENCES users(id)
);

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Remember Tokens Table
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Activity Log Table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Subscriptions Table
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_type ENUM('basic', 'premium', 'elite') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'cancelled', 'expired') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Client-Nutritionist Relationship Table
CREATE TABLE IF NOT EXISTS client_nutritionist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'client user ID',
    nutritionist_id INT NOT NULL,
    plan_type ENUM('premium', 'elite') NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (nutritionist_id) REFERENCES users(id)
);

-- Newsletter Subscriptions Table
CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (first_name, last_name, email, password, role, status, created_at)
VALUES ('Admin', 'User', 'admin@healthmantra.com', '$2y$10$w1Qw1Qw1Qw1Qw1Qw1Qw1Quw1Qw1Qw1Qw1Qw1Quw1Qw1Qw1Qw1Qw1Qw1', 'admin', 'active', NOW());

-- Insert default nutritionist users
INSERT INTO users (first_name, last_name, email, password, role, status, created_at)
VALUES 
('Jessica', 'Miller', 'jessica.miller@healthmantra.com', '$2y$10$8Qw1Qw1Qw1Qw1Qw1Qw1QuOQw1Qw1Qw1Qw1Qw1Quw1Qw1Qw1Qw1Qw1Qw1', 'nutritionist', 'active', NOW()),
('Michael', 'Johnson', 'michael.johnson@healthmantra.com', '$2y$10$8Qw1Qw1Qw1Qw1Qw1Qw1QuOQw1Qw1Qw1Qw1Qw1Quw1Qw1Qw1Qw1Qw1Qw1', 'nutritionist', 'active', NOW()),
('Sarah', 'Williams', 'sarah.williams@healthmantra.com', '$2y$10$8Qw1Qw1Qw1Qw1Qw1Qw1QuOQw1Qw1Qw1Qw1Qw1Quw1Qw1Qw1Qw1Qw1Qw1', 'nutritionist', 'active', NOW());

-- Insert sample supplements
INSERT INTO supplements (name, category, description, benefits, usage_instructions, price, stock_quantity, image, is_active, created_at, updated_at)
VALUES 
('Vitamin D3 + K2', 'vitamins', 'Premium vitamin D3 with K2 for optimal calcium absorption and bone health.', 'Supports bone health, immune function, and heart health.', 'Take 1 capsule daily with food.', 25.99, 100, 'vitamin_d3_k2.jpg', 1, NOW(), NOW()),
('Omega-3 Fish Oil', 'omega', 'Ultra-pure fish oil with optimal EPA and DHA levels for heart and brain health.', 'Supports heart health, brain function, and reduces inflammation.', 'Take 2 capsules daily with meals.', 29.99, 85, 'omega3.jpg', 1, NOW(), NOW()),
('Whey Protein Isolate', 'proteins', 'Pure whey protein isolate for maximum muscle recovery and growth. Low in lactose and fat.', 'Supports muscle growth, recovery, and weight management.', 'Mix 1 scoop with 8oz water or milk post-workout.', 39.99, 50, 'whey_protein.jpg', 1, NOW(), NOW()),
('Plant Protein Complex', 'proteins', 'Complete plant protein blend with all essential amino acids for optimal muscle support.', 'Supports muscle growth for plant-based diets.', 'Mix 1 scoop with 8oz water or plant milk daily.', 42.99, 65, 'plant_protein.jpg', 1, NOW(), NOW()),
('Magnesium Complex', 'minerals', 'High-absorption magnesium blend to support muscle function, sleep, and nervous system health.', 'Supports muscle relaxation, sleep quality, and stress management.', 'Take 1 capsule before bedtime.', 18.99, 120, 'magnesium.jpg', 1, NOW(), NOW()),
('Vitamin C Complex', 'vitamins', 'High-potency vitamin C with bioflavonoids for enhanced absorption and immune support.', 'Supports immune health, antioxidant protection, and skin health.', 'Take 1 capsule 1-2 times daily with meals.', 19.99, 95, 'vitamin_c.jpg', 1, NOW(), NOW()),
('Ashwagandha Root', 'herbs', 'Organic ashwagandha root extract for stress relief, energy support, and hormonal balance.', 'Supports stress response, energy levels, and hormone balance.', 'Take 1 capsule twice daily.', 22.99, 75, 'ashwagandha.jpg', 1, NOW(), NOW()),
('Turmeric Curcumin', 'herbs', 'High-potency turmeric with black pepper extract for enhanced absorption and inflammation support.', 'Supports healthy inflammation response and joint health.', 'Take 1 capsule with meals 1-2 times daily.', 24.99, 110, 'turmeric.jpg', 1, NOW(), NOW());