<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "campus_connect";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate new password hash
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin user password
$sql = "UPDATE users SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "Admin password has been reset successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='admin/login.php'>Go to Login Page</a>";
} else {
    echo "Error updating password: " . $conn->error;
}

// Close connection
$stmt->close();
$conn->close();
?>