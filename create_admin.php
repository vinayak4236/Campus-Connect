<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "campus_connect";

// Create database connection (without selecting a database)
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists, if not create it
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Check if users table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating users table: " . $conn->error);
}

// Check if admin user exists
$checkAdmin = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($checkAdmin);

if ($result->num_rows == 0) {
    // Create admin user
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $createAdmin = "INSERT INTO users (username, password, email) VALUES ('admin', '$adminPassword', 'admin@campusconnect.edu')";
    
    if ($conn->query($createAdmin) !== TRUE) {
        die("Error creating admin user: " . $conn->error);
    }
    
    echo "Admin user created successfully!<br>";
} else {
    // Update admin password
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $updateAdmin = "UPDATE users SET password = '$adminPassword' WHERE username = 'admin'";
    
    if ($conn->query($updateAdmin) !== TRUE) {
        die("Error updating admin password: " . $conn->error);
    }
    
    echo "Admin password updated successfully!<br>";
}

echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<a href='admin/login.php'>Go to Login Page</a>";

// Close connection
$conn->close();
?>