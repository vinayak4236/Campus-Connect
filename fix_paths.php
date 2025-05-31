<?php
// Fix script for path issues in event details

// 1. Update the js/api.js file to use the correct path for fetching a single event
$apiJs = file_get_contents('js/api.js');

// Update the fetchEventById function to use the dedicated event.php endpoint
$newApiJs = str_replace(
    '// Fetch event by ID
async function fetchEventById(id) {
    try {
        const events = await fetchEvents();
        return events.find(event => event.id === id);
    } catch (error) {
        console.error(\'Error fetching event by ID:\', error);
        return null;
    }
}',
    '// Fetch event by ID
async function fetchEventById(id) {
    try {
        const response = await fetch(`api/event.php?id=${id}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch event with ID ${id}`);
        }
        const event = await response.json();
        if (!event || event.error) {
            throw new Error(event.error || `Event with ID ${id} not found`);
        }
        return event;
    } catch (error) {
        console.error(\'Error fetching event by ID:\', error);
        
        // Fallback to static data if API fails
        if (typeof eventsData !== "undefined") {
            return eventsData.find(event => event.id === id);
        }
        return null;
    }
}',
    $apiJs
);

// Save the updated js/api.js file
file_put_contents('js/api.js', $newApiJs);

// 2. Update the event-details.html file to include the API.js file
$eventDetailsHtml = file_get_contents('event-details.html');

// Check if the API.js file is already included
if (strpos($eventDetailsHtml, 'js/api.js') === false) {
    // Add the API.js file before event-details.js
    $newEventDetailsHtml = str_replace(
        '<script src="events-data.js"></script>
    <script src="event-details.js"></script>',
        '<script src="events-data.js"></script>
    <script src="js/api.js"></script>
    <script src="event-details.js"></script>',
        $eventDetailsHtml
    );
    
    // Save the updated event-details.html file
    file_put_contents('event-details.html', $newEventDetailsHtml);
}

// 3. Update the event-details.js file to use the fetchEventById function from api.js
$eventDetailsJs = '// Event Details JavaScript for Campus Connect Portal

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
    
    // Fetch event using the API.js function
    fetchEventById(eventId)
        .then(event => {
            console.log("Event data received:", event);
            
            // Hide loading spinner
            document.getElementById("loading").style.display = "none";
            
            if (!event) {
                throw new Error("Event not found");
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
            console.error("Error loading event:", error);
            showError(error.message || "Event not found");
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
    fetchEvents()
        .then(events => {
            console.log("All events loaded for related events:", events.length);
            
            // Filter related events
            const relatedEvents = events.filter(event => relatedEventIds.includes(event.id));
            console.log("Related events found:", relatedEvents.length);
            
            if (relatedEvents.length === 0) {
                document.getElementById("relatedEventsSection").style.display = "none";
            } else {
                displayRelatedEvents(relatedEvents, container);
            }
        })
        .catch(error => {
            console.error("Error loading related events:", error);
            document.getElementById("relatedEventsSection").style.display = "none";
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
file_put_contents('event-details.js', $eventDetailsJs);

// 4. Create a test link page to verify the fix
$testLinkPage = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details Test Links</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Event Details Test Links</h1>
        <p>Click on the links below to test the event details page:</p>
        
        <div class="list-group mt-4" id="eventLinks">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading events...</p>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const eventLinks = document.getElementById("eventLinks");
            
            // Fetch events from API
            fetch("api/events.php")
                .then(response => response.json())
                .then(events => {
                    // Clear loading indicator
                    eventLinks.innerHTML = "";
                    
                    if (events.length === 0) {
                        eventLinks.innerHTML = "<p>No events found in the database.</p>";
                        return;
                    }
                    
                    // Add links for each event
                    events.forEach(event => {
                        const link = document.createElement("a");
                        link.href = `event-details.html?id=${event.id}`;
                        link.className = "list-group-item list-group-item-action";
                        link.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${event.title}</h5>
                                <small>ID: ${event.id}</small>
                            </div>
                            <p class="mb-1">${event.description.substring(0, 100)}...</p>
                            <small>${event.date} | ${event.location}</small>
                        `;
                        eventLinks.appendChild(link);
                    });
                })
                .catch(error => {
                    console.error("Error fetching events:", error);
                    eventLinks.innerHTML = `<p class="text-danger">Error loading events: ${error.message}</p>`;
                });
        });
    </script>
</body>
</html>';

// Save the test link page
file_put_contents('test_event_links.html', $testLinkPage);

echo "Path issues fixed! The event details page should now work correctly.";
echo "<p>Please check the <a href='test_event_links.html'>test links page</a> to verify the fix.</p>";
?>