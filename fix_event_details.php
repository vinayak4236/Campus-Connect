<?php
// Fix script for event details page

// Create a new event-details.js file
$newEventDetailsJs = '// Event Details JavaScript for Campus Connect Portal

document.addEventListener(\'DOMContentLoaded\', function() {
    // Get event ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = parseInt(urlParams.get(\'id\'));
    
    if (!eventId) {
        showError("Event ID not found in URL");
        return;
    }
    
    // Fetch event from API
    fetchEventById(eventId)
        .then(event => {
            if (!event) {
                throw new Error("Event not found");
            }
            
            // Load event details
            loadEventDetails(event);
            
            // Load related events
            if (event.relatedEvents && event.relatedEvents.length > 0) {
                loadRelatedEvents(event.relatedEvents);
            }
            
            // Initialize register button
            initRegisterButton();
        })
        .catch(error => {
            console.error("Error loading event:", error);
            showError(error.message || "Failed to load event");
        })
        .finally(() => {
            // Hide loading spinner
            document.getElementById(\'loading\').style.display = \'none\';
        });
});

// Fetch event by ID from API
async function fetchEventById(id) {
    try {
        // First try to get from API
        const response = await fetch(`api/events.php?id=${id}`);
        if (!response.ok) {
            throw new Error("Failed to fetch event from API");
        }
        
        const events = await response.json();
        
        // Check if we got the specific event
        const event = events.find(e => e.id === id);
        if (event) {
            return event;
        }
        
        // If not found in API response, try static data
        if (typeof eventsData !== "undefined") {
            const staticEvent = eventsData.find(e => e.id === id);
            if (staticEvent) {
                return staticEvent;
            }
        }
        
        throw new Error("Event not found");
    } catch (error) {
        console.error("Error in fetchEventById:", error);
        
        // Try to get from static data as fallback
        if (typeof eventsData !== "undefined") {
            const staticEvent = eventsData.find(e => e.id === id);
            if (staticEvent) {
                return staticEvent;
            }
        }
        
        throw error;
    }
}

// Fetch related events
async function fetchRelatedEvents(relatedEventIds) {
    try {
        // First try to get from API
        const response = await fetch(`api/events.php`);
        if (!response.ok) {
            throw new Error("Failed to fetch events from API");
        }
        
        const events = await response.json();
        
        // Filter related events
        return events.filter(event => relatedEventIds.includes(event.id));
    } catch (error) {
        console.error("Error in fetchRelatedEvents:", error);
        
        // Try to get from static data as fallback
        if (typeof eventsData !== "undefined") {
            return eventsData.filter(event => relatedEventIds.includes(event.id));
        }
        
        return [];
    }
}

// Load event details
function loadEventDetails(event) {
    // Clone the template
    const template = document.getElementById(\'eventDetailTemplate\');
    const clone = document.importNode(template.content, true);
    
    // Set event details
    clone.getElementById(\'eventTitle\').textContent = event.title;
    clone.getElementById(\'eventHeading\').textContent = event.title;
    
    // Set image with error handling
    const eventImage = clone.getElementById(\'eventImage\');
    eventImage.src = event.image;
    eventImage.alt = event.title;
    eventImage.style.maxHeight = \'500px\';
    eventImage.style.objectFit = \'cover\';
    eventImage.onerror = function() {
        console.log(\'Event image failed to load:\', event.image);
        this.onerror = null;
        this.src = `https://source.unsplash.com/1200x800/?${event.category.toLowerCase()},event`;
    };
    clone.getElementById(\'eventCategory\').textContent = event.category;
    clone.getElementById(\'eventCategory\').className = `badge bg-${event.categoryClass} me-2`;
    clone.getElementById(\'eventStatus\').textContent = event.status;
    clone.getElementById(\'eventStatus\').className = `badge bg-${event.statusClass}`;
    clone.getElementById(\'eventDescription\').textContent = event.description;
    clone.getElementById(\'eventDate\').textContent = event.date;
    clone.getElementById(\'eventTime\').textContent = event.time;
    clone.getElementById(\'eventLocation\').textContent = event.location;
    clone.getElementById(\'eventOrganizer\').textContent = event.organizer;
    clone.getElementById(\'registrationDeadline\').textContent = event.registrationDeadline;
    clone.getElementById(\'availableSeats\').textContent = event.availableSeats;
    clone.getElementById(\'contactEmail\').textContent = event.contactEmail;
    clone.getElementById(\'contactPhone\').textContent = event.contactPhone;
    
    // Add event schedule
    const scheduleTable = clone.getElementById(\'eventSchedule\');
    if (event.schedule && event.schedule.length > 0) {
        event.schedule.forEach(item => {
            const row = document.createElement(\'tr\');
            row.innerHTML = `
                <td>${item.time}</td>
                <td>${item.activity}</td>
                <td>${item.location}</td>
            `;
            scheduleTable.appendChild(row);
        });
    } else {
        // Hide schedule section if no schedule
        clone.getElementById(\'eventScheduleSection\').style.display = \'none\';
    }
    
    // Append to container
    document.getElementById(\'eventDetailsContainer\').appendChild(clone);
}

// Load related events
async function loadRelatedEvents(relatedEventIds) {
    try {
        // Get related events
        const relatedEvents = await fetchRelatedEvents(relatedEventIds);
        
        if (relatedEvents.length === 0) return;
        
        // Show related events section
        document.getElementById(\'relatedEventsSection\').style.display = \'block\';
        
        // Get container
        const container = document.getElementById(\'relatedEventsContainer\');
        
        // Get template
        const template = document.getElementById(\'relatedEventTemplate\');
        
        // Add related events
        relatedEvents.forEach(event => {
            const clone = document.importNode(template.content, true);
            
            // Set event details with error handling for images
            const relatedEventImage = clone.querySelector(\'.related-event-image\');
            relatedEventImage.src = event.image;
            relatedEventImage.alt = event.title;
            relatedEventImage.style.height = \'150px\';
            relatedEventImage.style.objectFit = \'cover\';
            relatedEventImage.onerror = function() {
                console.log(\'Related event image failed to load:\', event.image);
                this.onerror = null;
                this.src = `https://source.unsplash.com/600x400/?${event.category.toLowerCase()},event`;
            };
            clone.querySelector(\'.related-event-category\').textContent = event.category;
            clone.querySelector(\'.related-event-category\').className = `badge bg-${event.categoryClass} mb-2`;
            clone.querySelector(\'.related-event-title\').textContent = event.title;
            clone.querySelector(\'.related-event-date-location\').innerHTML = `<i class="fas fa-calendar-alt"></i> ${event.date} | <i class="fas fa-map-marker-alt"></i> ${event.location}`;
            clone.querySelector(\'.related-event-link\').href = `event-details.html?id=${event.id}`;
            
            // Append to container
            container.appendChild(clone);
        });
    } catch (error) {
        console.error("Error loading related events:", error);
    }
}

// Show error message
function showError(message) {
    const container = document.getElementById(\'eventDetailsContainer\');
    container.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error!</h4>
            <p>${message}</p>
            <hr>
            <p class="mb-0">Please go back to <a href="events.html" class="alert-link">Events</a> page.</p>
        </div>
    `;
    
    // Hide loading spinner
    document.getElementById(\'loading\').style.display = \'none\';
}

// Initialize register button
function initRegisterButton() {
    const registerButton = document.getElementById(\'registerButton\');
    if (!registerButton) return;
    
    registerButton.addEventListener(\'click\', function() {
        // Disable button
        registerButton.disabled = true;
        registerButton.innerHTML = \'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...\';
        
        // Simulate registration process
        setTimeout(function() {
            // Show success message
            document.getElementById(\'registrationSuccess\').style.display = \'block\';
            
            // Update button
            registerButton.innerHTML = \'Registered\';
            registerButton.classList.remove(\'btn-success\');
            registerButton.classList.add(\'btn-secondary\');
            
            // Update available seats
            const availableSeatsElement = document.getElementById(\'availableSeats\');
            let availableSeats = parseInt(availableSeatsElement.textContent);
            availableSeatsElement.textContent = availableSeats - 1;
        }, 1500);
    });
}';

// Save the new event-details.js file
file_put_contents('event-details.js', $newEventDetailsJs);

// Update the events.php API to handle single event requests
$eventsPhp = file_get_contents('api/events.php');

// Check if the API already handles single event requests
if (strpos($eventsPhp, 'if (isset($_GET[\'id\'])') === false) {
    // Add single event handling to the API
    $newEventsPhp = str_replace(
        '// Include database connection',
        '// Include database connection
// Check if a specific event ID is requested
$singleEventId = isset($_GET[\'id\']) ? intval($_GET[\'id\']) : null;',
        $eventsPhp
    );
    
    // Save the updated events.php file
    file_put_contents('api/events.php', $newEventsPhp);
}

// Update the event-details.html file to include the API.js file
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

echo "Event details page fixed successfully!";
?>