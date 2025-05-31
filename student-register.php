<?php
// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get database connection
$conn = require_once "../config/database.php";

// Get request data
$data = json_decode(file_get_contents("php://input"));

// Check if required fields are provided
if (!isset($data->name) || !isset($data->usn) || !isset($data->email) || !isset($data->password) || 
    !isset($data->department) || !isset($data->semester)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

// Sanitize input
$name = mysqli_real_escape_string($conn, $data->name);
$usn = mysqli_real_escape_string($conn, strtoupper($data->usn));
$email = mysqli_real_escape_string($conn, $data->email);
$phone = isset($data->phone) ? mysqli_real_escape_string($conn, $data->phone) : "";
$department = mysqli_real_escape_string($conn, $data->department);
$semester = mysqli_real_escape_string($conn, $data->semester);
$password = $data->password;

// Validate USN format
if (!preg_match('/^\d[A-Z]{2}\d{2}[A-Z]{2}\d{3}$/', $usn)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid USN format. Expected format: 1MS21CS001"
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email format"
    ]);
    exit;
}

// Validate password strength
if (strlen($password) < 8 || 
    !preg_match('/[A-Z]/', $password) || 
    !preg_match('/[0-9]/', $password) || 
    !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
    echo json_encode([
        "success" => false,
        "message" => "Password does not meet requirements"
    ]);
    exit;
}

// Check if student already exists
$check_query = "SELECT * FROM students WHERE usn = ? OR email = ? LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ss", $usn, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $existing_student = $check_result->fetch_assoc();
    
    if ($existing_student["usn"] === $usn) {
        echo json_encode([
            "success" => false,
            "message" => "A student with this USN already exists"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "A student with this email already exists"
        ]);
    }
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Create student in database
try {
    // First, check if the students table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'students'");
    
    if ($table_check->num_rows == 0) {
        // Create students table if it doesn't exist
        $create_table_query = "CREATE TABLE students (
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
        
        if (!$conn->query($create_table_query)) {
            throw new Exception("Error creating students table: " . $conn->error);
        }
    }
    
    // Insert student
    $insert_query = "INSERT INTO students (name, usn, email, phone, department, semester, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("sssssss", $name, $usn, $email, $phone, $department, $semester, $hashed_password);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Student registered successfully",
            "student_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Error registering student: " . $insert_stmt->error);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

// Close connection
$conn->close();
?>