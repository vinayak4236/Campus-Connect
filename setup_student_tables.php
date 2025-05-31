<?php
// Script to set up student-related database tables

// Include database connection
$conn = require_once "config/database.php";

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Setting up student tables</h1>";

// Create students table
$create_students_table = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    usn VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(50) NOT NULL,
    semester INT(2) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (usn),
    UNIQUE KEY (email)
)";

if ($conn->query($create_students_table)) {
    echo "<p>Students table created successfully or already exists.</p>";
} else {
    echo "<p>Error creating students table: " . $conn->error . "</p>";
}

// Create student_courses table
$create_student_courses_table = "CREATE TABLE IF NOT EXISTS student_courses (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    credits INT(2) NOT NULL,
    semester INT(2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";

if ($conn->query($create_student_courses_table)) {
    echo "<p>Student courses table created successfully or already exists.</p>";
} else {
    echo "<p>Error creating student courses table: " . $conn->error . "</p>";
}

// Create student_events table
$create_student_events_table = "CREATE TABLE IF NOT EXISTS student_events (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    event_id INT(11) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)";

if ($conn->query($create_student_events_table)) {
    echo "<p>Student events table created successfully or already exists.</p>";
} else {
    echo "<p>Error creating student events table: " . $conn->error . "</p>";
}

// Create student_clubs table
$create_student_clubs_table = "CREATE TABLE IF NOT EXISTS student_clubs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    club_id INT(11) NOT NULL,
    role VARCHAR(50) DEFAULT 'Member',
    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";

if ($conn->query($create_student_clubs_table)) {
    echo "<p>Student clubs table created successfully or already exists.</p>";
} else {
    echo "<p>Error creating student clubs table: " . $conn->error . "</p>";
}

// Create student_assignments table
$create_student_assignments_table = "CREATE TABLE IF NOT EXISTS student_assignments (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    deadline DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    submission_date TIMESTAMP NULL,
    grade VARCHAR(10) NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";

if ($conn->query($create_student_assignments_table)) {
    echo "<p>Student assignments table created successfully or already exists.</p>";
} else {
    echo "<p>Error creating student assignments table: " . $conn->error . "</p>";
}

// Insert a demo student account
$check_demo_student = "SELECT * FROM students WHERE usn = '1MS21CS001'";
$result = $conn->query($check_demo_student);

if ($result->num_rows == 0) {
    // Demo student doesn't exist, create one
    $demo_password = password_hash('password123', PASSWORD_DEFAULT);
    
    $insert_demo_student = "INSERT INTO students (name, usn, email, phone, department, semester, password) 
                           VALUES ('Demo Student', '1MS21CS001', 'demo.student@example.com', '9876543210', 'CSE', 5, ?)";
    
    $stmt = $conn->prepare($insert_demo_student);
    $stmt->bind_param("s", $demo_password);
    
    if ($stmt->execute()) {
        echo "<p>Demo student account created successfully.</p>";
        echo "<p>Login credentials:</p>";
        echo "<ul>";
        echo "<li>Name: Demo Student</li>";
        echo "<li>USN: 1MS21CS001</li>";
        echo "<li>Password: password123</li>";
        echo "</ul>";
    } else {
        echo "<p>Error creating demo student account: " . $stmt->error . "</p>";
    }
} else {
    echo "<p>Demo student account already exists.</p>";
}

// Close connection
$conn->close();

echo "<p>Setup completed. <a href='student-login.html'>Go to Student Login</a></p>";
?>