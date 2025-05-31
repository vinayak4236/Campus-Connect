<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database connection
$conn = require_once "../config/database.php";

// Get all announcements
$announcements = [];
$result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate display date
        $announcement_date = new DateTime($row['date']);
        $current_date = new DateTime();
        $interval = $current_date->diff($announcement_date);
        
        if ($interval->days == 0) {
            $display_date = "Today";
        } elseif ($interval->days == 1) {
            $display_date = "Yesterday";
        } elseif ($interval->days < 7) {
            $display_date = $interval->days . " days ago";
        } elseif ($interval->days < 14) {
            $display_date = "1 week ago";
        } elseif ($interval->days < 30) {
            $display_date = floor($interval->days / 7) . " weeks ago";
        } elseif ($interval->days < 60) {
            $display_date = "1 month ago";
        } else {
            $display_date = floor($interval->days / 30) . " months ago";
        }
        
        // Add announcement to array
        $announcements[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'categoryClass' => $row['category_class'],
            'date' => $row['date'],
            'displayDate' => $display_date,
            'content' => $row['content'],
            'author' => $row['author'],
            'priority' => $row['priority']
        ];
    }
}

// Close connection
$conn->close();

// Return announcements as JSON
echo json_encode($announcements);
?>