<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database connection
$conn = require_once "../config/database.php";

// Get all clubs
$clubs = [];
$result = $conn->query("SELECT * FROM clubs ORDER BY name ASC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add club to array
        $clubs[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'categoryClass' => $row['category_class'],
            'description' => $row['description'],
            'meetingSchedule' => [
                'days' => $row['meeting_days'],
                'time' => $row['meeting_time'],
                'location' => $row['meeting_location']
            ],
            'members' => (int)$row['members'],
            'image' => $row['image']
        ];
    }
}

// Close connection
$conn->close();

// Return clubs as JSON
echo json_encode($clubs);
?>