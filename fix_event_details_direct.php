<?php
// Direct fix for event details page

// 1. First, create a dedicated API endpoint for fetching a single event
$singleEventApiContent = '<?php
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
?>';

// Create the single event API endpoint
file_put_contents('api/event.php', $singleEventApiContent);

// 2. Now, update the event-details.js file
$eventDetailsJsContent = '// Event Details JavaScript for Campus Connect Portal

document.addEventListener("DOMContentLoaded", function() {
    // Get event ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = parseInt(urlParams.get("id"));
    
    if (!eventId) {
        showError("Event ID not found in URL");
        return;
    }
    
    // Show loading spinner
    document.getElementById("loading").style.display = "block";
    
    // Fetch event from API
    fetch(`api/event.php?id=${eventId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            return response.json();
        })
        .then(event => {
            // Hide loading spinner
            document.getElementById("loading").style.display = "none";
            
            if (!event || event.error) {
                throw new Error(event.error || "Event not found");
            }
            
            console.log("Event loaded from API:", event);
            
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
            if (typeof eventsData !== "undefined") {
                const staticEvent = eventsData.find(e => e.id === eventId);
                
                if (staticEvent) {
                    console.log("Loading event from static data:", staticEvent);
                    
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
                    showError("Event not found");
                }
            } else {
                showError("Event not found");
            }
        });
});

// Load event details
function loadEventDetails(event) {
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
}

// Load related events
function loadRelatedEvents(relatedEventIds) {
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
            // Filter related events
            const relatedEvents = events.filter(event => relatedEventIds.includes(event.id));
            
            if (relatedEvents.length === 0) {
                // Try to get from static data
                if (typeof eventsData !== "undefined") {
                    const staticRelatedEvents = eventsData.filter(event => relatedEventIds.includes(event.id));
                    if (staticRelatedEvents.length > 0) {
                        displayRelatedEvents(staticRelatedEvents, container);
                    } else {
                        document.getElementById("relatedEventsSection").style.display = "none";
                    }
                } else {
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
                const staticRelatedEvents = eventsData.filter(event => relatedEventIds.includes(event.id));
                if (staticRelatedEvents.length > 0) {
                    displayRelatedEvents(staticRelatedEvents, container);
                } else {
                    document.getElementById("relatedEventsSection").style.display = "none";
                }
            } else {
                document.getElementById("relatedEventsSection").style.display = "none";
            }
        });
}

// Display related events
function displayRelatedEvents(relatedEvents, container) {
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
}

// Show error message
function showError(message) {
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
    const registerButton = document.getElementById("registerButton");
    if (!registerButton) return;
    
    registerButton.addEventListener("click", function() {
        // Disable button
        registerButton.disabled = true;
        registerButton.innerHTML = \'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...\';
        
        // Simulate registration process
        setTimeout(function() {
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

// 3. Update the event-details.html file to include the API.js file
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
}

// 4. Update the events.html file to use the correct link to event details
$eventsHtml = file_get_contents('events.html');

// Make sure the View Details links are correct
$newEventsHtml = str_replace(
    'href="event-details.html?id=${event.id}"',
    'href="event-details.html?id=${event.id}"',
    $eventsHtml
);

// Save the updated events.html file
file_put_contents('events.html', $newEventsHtml);

echo "Event details page fixed successfully! A dedicated API endpoint for single events has been created.";
?>