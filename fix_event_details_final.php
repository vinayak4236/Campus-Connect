<?php
// Final fix for event details page with debugging

// 1. First, create a debug log function
function debug_log($message, $data = null) {
    $log_file = 'debug_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}";
    
    if ($data !== null) {
        $log_message .= ": " . print_r($data, true);
    }
    
    file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
}

// Start logging
debug_log("Starting event details fix");

// 2. Create a more robust API endpoint for fetching a single event
$singleEventApiContent = '<?php
// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Enable error reporting for debugging
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Log function
function api_log($message, $data = null) {
    $log_file = "../api_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    $log_message = "[{$timestamp}] {$message}";
    
    if ($data !== null) {
        $log_message .= ": " . print_r($data, true);
    }
    
    file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
}

// Start logging
api_log("API request received", $_GET);

// Include database connection
$conn = require_once "../config/database.php";

// Check if event ID is provided
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    $error = ["error" => "Event ID is required"];
    api_log("Error", $error);
    echo json_encode($error);
    exit;
}

// Get event ID
$eventId = intval($_GET["id"]);
api_log("Looking for event with ID", $eventId);

// Get event details
$event = null;

try {
    // First, check if the event exists
    $check_query = "SELECT COUNT(*) as count FROM events WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $eventId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();
    
    api_log("Event count", $check_row["count"]);
    
    if ($check_row["count"] == 0) {
        $error = ["error" => "Event not found"];
        api_log("Error", $error);
        echo json_encode($error);
        exit;
    }
    
    // Get the event details
    $query = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        api_log("Event found", $row["title"]);
        
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
    } else {
        $error = ["error" => "Event not found"];
        api_log("Error", $error);
        echo json_encode($error);
        exit;
    }
} catch (Exception $e) {
    $error = ["error" => "Database error: " . $e->getMessage()];
    api_log("Exception", $error);
    echo json_encode($error);
    exit;
}

// Close connection
$conn->close();

// Return event as JSON
api_log("Returning event data");
echo json_encode($event);
?>';

// Create the single event API endpoint
file_put_contents('api/event.php', $singleEventApiContent);
debug_log("Created api/event.php");

// 3. Now, update the event-details.js file with better debugging
$eventDetailsJsContent = '// Event Details JavaScript for Campus Connect Portal

document.addEventListener("DOMContentLoaded", function() {
    console.log("Event details page loaded");
    
    // Get event ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = parseInt(urlParams.get("id"));
    
    console.log("Event ID from URL:", eventId);
    
    if (!eventId) {
        showError("Event ID not found in URL");
        return;
    }
    
    // Show loading spinner
    document.getElementById("loading").style.display = "block";
    
    // Fetch event from API
    console.log("Fetching event from API:", `api/event.php?id=${eventId}`);
    
    fetch(`api/event.php?id=${eventId}`)
        .then(response => {
            console.log("API response status:", response.status);
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            return response.json();
        })
        .then(event => {
            console.log("Event data received:", event);
            
            // Hide loading spinner
            document.getElementById("loading").style.display = "none";
            
            if (!event || event.error) {
                throw new Error(event.error || "Event not found");
            }
            
            // Load event details
            loadEventDetails(event);
            
            // Load related events if any
            if (event.relatedEvents && event.relatedEvents.length > 0) {
                loadRelatedEvents(event.relatedEvents);
            }
            
            // Initialize register button
            initRegisterButton();
        })
        .catch(error => {
            console.error("Error loading event from API:", error);
            
            // Try to load from static data as fallback
            console.log("Trying to load from static data");
            
            if (typeof eventsData !== "undefined") {
                console.log("Static data available, searching for event ID:", eventId);
                const staticEvent = eventsData.find(e => e.id === eventId);
                
                if (staticEvent) {
                    console.log("Event found in static data:", staticEvent);
                    
                    // Hide loading spinner
                    document.getElementById("loading").style.display = "none";
                    
                    // Load event details
                    loadEventDetails(staticEvent);
                    
                    // Load related events if any
                    if (staticEvent.relatedEvents && staticEvent.relatedEvents.length > 0) {
                        loadRelatedEvents(staticEvent.relatedEvents);
                    }
                    
                    // Initialize register button
                    initRegisterButton();
                } else {
                    console.log("Event not found in static data");
                    showError("Event not found");
                }
            } else {
                console.log("No static data available");
                showError("Event not found");
            }
        });
});

// Load event details
function loadEventDetails(event) {
    console.log("Loading event details for:", event.title);
    
    // Clone the template
    const template = document.getElementById("eventDetailTemplate");
    const clone = document.importNode(template.content, true);
    
    // Set event details
    clone.getElementById("eventTitle").textContent = event.title;
    clone.getElementById("eventHeading").textContent = event.title;
    
    // Set image with error handling
    const eventImage = clone.getElementById("eventImage");
    eventImage.src = event.image;
    eventImage.alt = event.title;
    eventImage.style.maxHeight = "500px";
    eventImage.style.objectFit = "cover";
    eventImage.onerror = function() {
        console.log("Event image failed to load:", event.image);
        this.onerror = null;
        this.src = `https://source.unsplash.com/1200x800/?${event.category.toLowerCase()},event`;
    };
    clone.getElementById("eventCategory").textContent = event.category;
    clone.getElementById("eventCategory").className = `badge bg-${event.categoryClass} me-2`;
    clone.getElementById("eventStatus").textContent = event.status;
    clone.getElementById("eventStatus").className = `badge bg-${event.statusClass}`;
    clone.getElementById("eventDescription").textContent = event.description;
    clone.getElementById("eventDate").textContent = event.date;
    clone.getElementById("eventTime").textContent = event.time;
    clone.getElementById("eventLocation").textContent = event.location;
    clone.getElementById("eventOrganizer").textContent = event.organizer;
    clone.getElementById("registrationDeadline").textContent = event.registrationDeadline;
    clone.getElementById("availableSeats").textContent = event.availableSeats;
    clone.getElementById("contactEmail").textContent = event.contactEmail;
    clone.getElementById("contactPhone").textContent = event.contactPhone;
    
    // Add event schedule
    const scheduleTable = clone.getElementById("eventSchedule");
    if (event.schedule && event.schedule.length > 0) {
        event.schedule.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.time}</td>
                <td>${item.activity}</td>
                <td>${item.location}</td>
            `;
            scheduleTable.appendChild(row);
        });
    } else {
        // Hide schedule section if no schedule
        clone.getElementById("eventScheduleSection").style.display = "none";
    }
    
    // Append to container
    document.getElementById("eventDetailsContainer").appendChild(clone);
    console.log("Event details loaded successfully");
}

// Load related events
function loadRelatedEvents(relatedEventIds) {
    console.log("Loading related events:", relatedEventIds);
    
    if (!relatedEventIds || relatedEventIds.length === 0) return;
    
    // Show related events section
    document.getElementById("relatedEventsSection").style.display = "block";
    
    // Get container
    const container = document.getElementById("relatedEventsContainer");
    
    // Fetch all events to find related ones
    fetch("api/events.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            return response.json();
        })
        .then(events => {
            console.log("All events loaded for related events:", events.length);
            
            // Filter related events
            const relatedEvents = events.filter(event => relatedEventIds.includes(event.id));
            console.log("Related events found:", relatedEvents.length);
            
            if (relatedEvents.length === 0) {
                // Try to get from static data
                if (typeof eventsData !== "undefined") {
                    console.log("Trying to find related events in static data");
                    const staticRelatedEvents = eventsData.filter(event => relatedEventIds.includes(event.id));
                    if (staticRelatedEvents.length > 0) {
                        console.log("Related events found in static data:", staticRelatedEvents.length);
                        displayRelatedEvents(staticRelatedEvents, container);
                    } else {
                        console.log("No related events found in static data");
                        document.getElementById("relatedEventsSection").style.display = "none";
                    }
                } else {
                    console.log("No static data available for related events");
                    document.getElementById("relatedEventsSection").style.display = "none";
                }
            } else {
                displayRelatedEvents(relatedEvents, container);
            }
        })
        .catch(error => {
            console.error("Error loading related events:", error);
            
            // Try to get from static data
            if (typeof eventsData !== "undefined") {
                console.log("Trying to find related events in static data after API error");
                const staticRelatedEvents = eventsData.filter(event => relatedEventIds.includes(event.id));
                if (staticRelatedEvents.length > 0) {
                    console.log("Related events found in static data:", staticRelatedEvents.length);
                    displayRelatedEvents(staticRelatedEvents, container);
                } else {
                    console.log("No related events found in static data");
                    document.getElementById("relatedEventsSection").style.display = "none";
                }
            } else {
                console.log("No static data available for related events");
                document.getElementById("relatedEventsSection").style.display = "none";
            }
        });
}

// Display related events
function displayRelatedEvents(relatedEvents, container) {
    console.log("Displaying related events:", relatedEvents.length);
    
    // Get template
    const template = document.getElementById("relatedEventTemplate");
    
    // Add related events
    relatedEvents.forEach(event => {
        const clone = document.importNode(template.content, true);
        
        // Set event details with error handling for images
        const relatedEventImage = clone.querySelector(".related-event-image");
        relatedEventImage.src = event.image;
        relatedEventImage.alt = event.title;
        relatedEventImage.style.height = "150px";
        relatedEventImage.style.objectFit = "cover";
        relatedEventImage.onerror = function() {
            console.log("Related event image failed to load:", event.image);
            this.onerror = null;
            this.src = `https://source.unsplash.com/600x400/?${event.category.toLowerCase()},event`;
        };
        clone.querySelector(".related-event-category").textContent = event.category;
        clone.querySelector(".related-event-category").className = `badge bg-${event.categoryClass} mb-2`;
        clone.querySelector(".related-event-title").textContent = event.title;
        clone.querySelector(".related-event-date-location").innerHTML = `<i class="fas fa-calendar-alt"></i> ${event.date} | <i class="fas fa-map-marker-alt"></i> ${event.location}`;
        clone.querySelector(".related-event-link").href = `event-details.html?id=${event.id}`;
        
        // Append to container
        container.appendChild(clone);
    });
    
    console.log("Related events displayed successfully");
}

// Show error message
function showError(message) {
    console.error("Showing error message:", message);
    
    const container = document.getElementById("eventDetailsContainer");
    container.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error!</h4>
            <p>${message}</p>
            <hr>
            <p class="mb-0">Please go back to <a href="events.html" class="alert-link">Events</a> page.</p>
        </div>
    `;
    
    // Hide loading spinner
    document.getElementById("loading").style.display = "none";
}

// Initialize register button
function initRegisterButton() {
    console.log("Initializing register button");
    
    const registerButton = document.getElementById("registerButton");
    if (!registerButton) {
        console.log("Register button not found");
        return;
    }
    
    registerButton.addEventListener("click", function() {
        console.log("Register button clicked");
        
        // Disable button
        registerButton.disabled = true;
        registerButton.innerHTML = \'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...\';
        
        // Simulate registration process
        setTimeout(function() {
            console.log("Registration process completed");
            
            // Show success message
            document.getElementById("registrationSuccess").style.display = "block";
            
            // Update button
            registerButton.innerHTML = "Registered";
            registerButton.classList.remove("btn-success");
            registerButton.classList.add("btn-secondary");
            
            // Update available seats
            const availableSeatsElement = document.getElementById("availableSeats");
            let availableSeats = parseInt(availableSeatsElement.textContent);
            availableSeatsElement.textContent = availableSeats - 1;
        }, 1500);
    });
}';

// Save the updated event-details.js file
file_put_contents('event-details.js', $eventDetailsJsContent);
debug_log("Updated event-details.js");

// 4. Create a direct API test script
$apiTestContent = '<?php
// Test script for event API

// Enable error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Include database connection
$conn = require_once "config/database.php";

echo "<h1>Event API Test</h1>";

// Get all events from the database
$query = "SELECT * FROM events";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h2>Events in Database</h2>";
    echo "<table border=\'1\' cellpadding=\'5\'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Location</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["title"] . "</td>";
        echo "<td>" . $row["category"] . "</td>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["location"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Reset result pointer
    $result->data_seek(0);
    $firstEvent = $result->fetch_assoc();
    $eventId = $firstEvent["id"];
    
    echo "<h2>Testing API with Event ID: " . $eventId . "</h2>";
    
    // Test the API directly
    $apiUrl = "http://localhost/campus-connect/api/event.php?id=" . $eventId;
    echo "<p>API URL: <a href=\'" . $apiUrl . "\' target=\'_blank\'>" . $apiUrl . "</a></p>";
    
    $apiResponse = @file_get_contents($apiUrl);
    
    if ($apiResponse === false) {
        echo "<p style=\'color:red\'>Error fetching API response: " . error_get_last()["message"] . "</p>";
    } else {
        echo "<h3>API Response:</h3>";
        echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
        
        // Parse the JSON response
        $eventData = json_decode($apiResponse, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p style=\'color:red\'>Error parsing JSON: " . json_last_error_msg() . "</p>";
        } else {
            echo "<h3>Parsed Event Data:</h3>";
            echo "<table border=\'1\' cellpadding=\'5\'>";
            
            foreach ($eventData as $key => $value) {
                echo "<tr>";
                echo "<th>" . htmlspecialchars($key) . "</th>";
                echo "<td>";
                
                if (is_array($value)) {
                    echo "<pre>" . htmlspecialchars(print_r($value, true)) . "</pre>";
                } else {
                    echo htmlspecialchars($value);
                }
                
                echo "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    }
    
    // Test the event details page
    $eventDetailsUrl = "http://localhost/campus-connect/event-details.html?id=" . $eventId;
    echo "<p>Event Details URL: <a href=\'" . $eventDetailsUrl . "\' target=\'_blank\'>" . $eventDetailsUrl . "</a></p>";
} else {
    echo "<p style=\'color:red\'>No events found in the database.</p>";
    
    // Create a sample event
    echo "<h2>Creating a Sample Event</h2>";
    
    $sampleEvent = [
        "title" => "Sample Test Event",
        "category" => "Academic",
        "category_class" => "primary",
        "status" => "Upcoming",
        "status_class" => "success",
        "description" => "This is a sample event created for testing purposes.",
        "date" => "2023-12-31",
        "time" => "10:00 AM - 12:00 PM",
        "location" => "Main Campus, Room 101",
        "organizer" => "Test Department",
        "image" => "https://source.unsplash.com/1200x800/?academic,event",
        "registration_deadline" => "2023-12-25",
        "available_seats" => 50,
        "contact_email" => "test@example.com",
        "contact_phone" => "123-456-7890"
    ];
    
    $insertQuery = "INSERT INTO events (title, category, category_class, status, status_class, description, date, time, location, organizer, image, registration_deadline, available_seats, contact_email, contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssssssssssisss", 
        $sampleEvent["title"],
        $sampleEvent["category"],
        $sampleEvent["category_class"],
        $sampleEvent["status"],
        $sampleEvent["status_class"],
        $sampleEvent["description"],
        $sampleEvent["date"],
        $sampleEvent["time"],
        $sampleEvent["location"],
        $sampleEvent["organizer"],
        $sampleEvent["image"],
        $sampleEvent["registration_deadline"],
        $sampleEvent["available_seats"],
        $sampleEvent["contact_email"],
        $sampleEvent["contact_phone"]
    );
    
    if ($stmt->execute()) {
        $eventId = $conn->insert_id;
        echo "<p style=\'color:green\'>Sample event created with ID: " . $eventId . "</p>";
        
        // Test the API with the new event
        echo "<h2>Testing API with New Event ID: " . $eventId . "</h2>";
        
        $apiUrl = "http://localhost/campus-connect/api/event.php?id=" . $eventId;
        echo "<p>API URL: <a href=\'" . $apiUrl . "\' target=\'_blank\'>" . $apiUrl . "</a></p>";
        
        $apiResponse = @file_get_contents($apiUrl);
        
        if ($apiResponse === false) {
            echo "<p style=\'color:red\'>Error fetching API response: " . error_get_last()["message"] . "</p>";
        } else {
            echo "<h3>API Response:</h3>";
            echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
        }
        
        // Test the event details page
        $eventDetailsUrl = "http://localhost/campus-connect/event-details.html?id=" . $eventId;
        echo "<p>Event Details URL: <a href=\'" . $eventDetailsUrl . "\' target=\'_blank\'>" . $eventDetailsUrl . "</a></p>";
    } else {
        echo "<p style=\'color:red\'>Error creating sample event: " . $stmt->error . "</p>";
    }
}

// Close connection
$conn->close();
?>';

// Save the API test script
file_put_contents('test_event_api.php', $apiTestContent);
debug_log("Created test_event_api.php");

// 5. Update the event-details.html file to include the API.js file
$eventDetailsHtml = file_get_contents('event-details.html');

// Check if the API.js file is already included
if (strpos($eventDetailsHtml, 'js/api.js') === false) {
    // Add the API.js file
    $newEventDetailsHtml = str_replace(
        '<script src="events-data.js"></script>',
        '<script src="events-data.js"></script>
    <script src="js/api.js"></script>',
        $eventDetailsHtml
    );
    
    // Save the updated event-details.html file
    file_put_contents('event-details.html', $newEventDetailsHtml);
    debug_log("Updated event-details.html");
}

echo "Event details page fixed with comprehensive debugging and logging!";
echo "<p>Please run the <a href='test_event_api.php'>API test script</a> to verify the fix.</p>";
?>