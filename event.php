<?php
// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include database connection
$conn = require_once "../config/database.php";

// Check if event ID is provided
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    echo json_encode(["error" => "Event ID is required"]);
    exit;
}

// Get event ID
$eventId = intval($_GET["id"]);

// Get event details
$event = null;
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Get schedule items for this event
    $schedule = [];
    $schedule_query = "SELECT * FROM event_schedule WHERE event_id = ? ORDER BY id ASC";
    $schedule_stmt = $conn->prepare($schedule_query);
    $schedule_stmt->bind_param("i", $eventId);
    $schedule_stmt->execute();
    $schedule_result = $schedule_stmt->get_result();
    
    if ($schedule_result->num_rows > 0) {
        while ($schedule_row = $schedule_result->fetch_assoc()) {
            $schedule[] = [
                "time" => $schedule_row["time"],
                "activity" => $schedule_row["activity"],
                "location" => $schedule_row["location"]
            ];
        }
    }
    
    // Get related events for this event
    $related_events = [];
    $related_query = "SELECT related_event_id FROM event_related WHERE event_id = ?";
    $related_stmt = $conn->prepare($related_query);
    $related_stmt->bind_param("i", $eventId);
    $related_stmt->execute();
    $related_result = $related_stmt->get_result();
    
    if ($related_result->num_rows > 0) {
        while ($related_row = $related_result->fetch_assoc()) {
            $related_events[] = (int)$related_row["related_event_id"];
        }
    }
    
    // Build event object
    $event = [
        "id" => (int)$row["id"],
        "title" => $row["title"],
        "category" => $row["category"],
        "categoryClass" => $row["category_class"],
        "status" => $row["status"],
        "statusClass" => $row["status_class"],
        "description" => $row["description"],
        "date" => $row["date"],
        "time" => $row["time"],
        "location" => $row["location"],
        "organizer" => $row["organizer"],
        "image" => $row["image"],
        "registrationDeadline" => $row["registration_deadline"],
        "availableSeats" => (int)$row["available_seats"],
        "contactEmail" => $row["contact_email"],
        "contactPhone" => $row["contact_phone"],
        "schedule" => $schedule,
        "relatedEvents" => $related_events
    ];
}

// Close connection
$conn->close();

// Return event as JSON
echo json_encode($event);
?>