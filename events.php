<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database connection
$conn = require_once "../config/database.php";

// Check if a specific event ID is requested
$singleEventId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Build the query based on whether we're fetching a single event or all events
$query = "SELECT * FROM events";
if ($singleEventId) {
    $query .= " WHERE id = " . $singleEventId;
}
$query .= " ORDER BY date ASC";

// Get events
$events = [];
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get schedule items for this event
        $schedule = [];
        $schedule_result = $conn->query("SELECT * FROM event_schedule WHERE event_id = " . $row['id'] . " ORDER BY id ASC");
        
        if ($schedule_result->num_rows > 0) {
            while ($schedule_row = $schedule_result->fetch_assoc()) {
                $schedule[] = [
                    'time' => $schedule_row['time'],
                    'activity' => $schedule_row['activity'],
                    'location' => $schedule_row['location']
                ];
            }
        }
        
        // Get related events for this event
        $related_events = [];
        $related_result = $conn->query("SELECT related_event_id FROM event_related WHERE event_id = " . $row['id']);
        
        if ($related_result->num_rows > 0) {
            while ($related_row = $related_result->fetch_assoc()) {
                $related_events[] = (int)$related_row['related_event_id'];
            }
        }
        
        // Add event to array
        $events[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'categoryClass' => $row['category_class'],
            'status' => $row['status'],
            'statusClass' => $row['status_class'],
            'description' => $row['description'],
            'date' => $row['date'],
            'time' => $row['time'],
            'location' => $row['location'],
            'organizer' => $row['organizer'],
            'image' => $row['image'],
            'registrationDeadline' => $row['registration_deadline'],
            'availableSeats' => (int)$row['available_seats'],
            'contactEmail' => $row['contact_email'],
            'contactPhone' => $row['contact_phone'],
            'schedule' => $schedule,
            'relatedEvents' => $related_events
        ];
    }
}

// Close connection
$conn->close();

// Return events as JSON
echo json_encode($events);
?>