<?php
// Include database connection
$conn = require_once "config/database.php";

// Check if users table exists and has admin user
$sql = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Admin user exists:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Password hash: " . $user['password'] . "<br>";
    
    // Check if the password 'admin123' matches the stored hash
    if (password_verify('admin123', $user['password'])) {
        echo "<br>Password 'admin123' is correct for this user.";
    } else {
        echo "<br>Password 'admin123' does NOT match the stored hash.";
    }
} else {
    echo "Admin user not found in the database.";
}

// Close connection
$conn->close();
?>