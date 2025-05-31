<?php
// Debug script to check events in the database

// Include database connection
$conn = require_once "config/database.php";

// Get all events from the database
$query = "SELECT * FROM events";
$result = $conn->query($query);

echo "<h1>Events in Database</h1>";

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Location</th><th>Image</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['category'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['location'] . "</td>";
        echo "<td>" . $row['image'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No events found in the database.</p>";
}

// Check if the event.php API is working
echo "<h2>Testing event.php API</h2>";

// Get the first event ID from the database
$conn->data_seek(0); // Reset result pointer to beginning
if ($result->num_rows > 0) {
    $firstEvent = $result->fetch_assoc();
    $firstEventId = $firstEvent['id'];
    
    echo "<p>Testing API with event ID: " . $firstEventId . "</p>";
    
    // Make a request to the API
    $apiUrl = "http://localhost/campus-connect/api/event.php?id=" . $firstEventId;
    $apiResponse = file_get_contents($apiUrl);
    
    echo "<h3>API Response:</h3>";
    echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
} else {
    echo "<p>Cannot test API - no events in database.</p>";
}

// Close connection
$conn->close();
?>