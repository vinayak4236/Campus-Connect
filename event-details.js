// Event Details JavaScript for Campus Connect Portal

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
        registerButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
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
}