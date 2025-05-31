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
if (!isset($data->usn) || !isset($data->password) || !isset($data->name)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

// Sanitize input
$name = mysqli_real_escape_string($conn, $data->name);
$usn = mysqli_real_escape_string($conn, $data->usn);
$password = $data->password;

// Check if student exists in database
$query = "SELECT * FROM students WHERE usn = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $usn);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Student found, verify password
    $student = $result->fetch_assoc();
    
    if (password_verify($password, $student["password"])) {
        // Password is correct, check if name matches
        if ($student["name"] === $name) {
            // Login successful
            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "student" => [
                    "id" => $student["id"],
                    "name" => $student["name"],
                    "usn" => $student["usn"],
                    "email" => $student["email"]
                ]
            ]);
        } else {
            // Name doesn't match
            echo json_encode([
                "success" => false,
                "message" => "Invalid credentials. Name does not match USN."
            ]);
        }
    } else {
        // Password is incorrect
        echo json_encode([
            "success" => false,
            "message" => "Invalid password"
        ]);
    }
} else {
    // Student not found, check if this is a demo account
    if ($usn === "1MS21CS001" && $password === "password123" && $name === "Demo Student") {
        // Demo account, return success
        echo json_encode([
            "success" => true,
            "message" => "Login successful (Demo Account)",
            "student" => [
                "id" => 1,
                "name" => "Demo Student",
                "usn" => "1MS21CS001",
                "email" => "demo.student@example.com"
            ]
        ]);
    } else {
        // Student not found
        echo json_encode([
            "success" => false,
            "message" => "Student not found with the provided USN"
        ]);
    }
}

// Close connection
$conn->close();
?>