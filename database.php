<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "campus_connect";

// Create database connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Create tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS `events` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `category_class` VARCHAR(50) NOT NULL,
        `status` VARCHAR(50) NOT NULL,
        `status_class` VARCHAR(50) NOT NULL,
        `description` TEXT NOT NULL,
        `date` VARCHAR(50) NOT NULL,
        `time` VARCHAR(50) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `organizer` VARCHAR(255) NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `registration_deadline` VARCHAR(50) NOT NULL,
        `available_seats` INT NOT NULL,
        `contact_email` VARCHAR(100) NOT NULL,
        `contact_phone` VARCHAR(50) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS `event_schedule` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `event_id` INT NOT NULL,
        `time` VARCHAR(100) NOT NULL,
        `activity` VARCHAR(255) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS `event_related` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `event_id` INT NOT NULL,
        `related_event_id` INT NOT NULL,
        FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`related_event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS `clubs` (
        `id` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `category_class` VARCHAR(50) NOT NULL,
        `description` TEXT NOT NULL,
        `meeting_days` VARCHAR(100) NOT NULL,
        `meeting_time` VARCHAR(50) NOT NULL,
        `meeting_location` VARCHAR(255) NOT NULL,
        `members` INT NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS `announcements` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `category_class` VARCHAR(50) NOT NULL,
        `date` DATE NOT NULL,
        `content` TEXT NOT NULL,
        `author` VARCHAR(100) NOT NULL,
        `priority` VARCHAR(20) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }
}

// Check if admin user exists, if not create one
$checkAdmin = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($checkAdmin);

if ($result->num_rows == 0) {
    // Create default admin user
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $createAdmin = "INSERT INTO users (username, password, email) VALUES ('admin', '$adminPassword', 'admin@campusconnect.edu')";
    
    if ($conn->query($createAdmin) !== TRUE) {
        die("Error creating admin user: " . $conn->error);
    }
}

// Return the connection
return $conn;
?>