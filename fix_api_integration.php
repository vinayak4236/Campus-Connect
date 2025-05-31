<?php
// Fix script for API integration issues

// 1. First, let's fix the events.php API to handle single event requests
$eventsPhp = file_get_contents('api/events.php');

// Update the events.php API to handle single event requests
$newEventsPhp = '<?php
// Set headers for JSON response
header(\'Content-Type: application/json\');
header(\'Access-Control-Allow-Origin: *\');

// Include database connection
$conn = require_once "../config/database.php";

// Check if a specific event ID is requested
$singleEventId = isset($_GET[\'id\']) ? intval($_GET[\'id\']) : null;

// Build the query based on whether we\'re fetching a single event or all events
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
        $schedule_result = $conn->query("SELECT * FROM event_schedule WHERE event_id = " . $row[\'id\'] . " ORDER BY id ASC");
        
        if ($schedule_result->num_rows > 0) {
            while ($schedule_row = $schedule_result->fetch_assoc()) {
                $schedule[] = [
                    \'time\' => $schedule_row[\'time\'],
                    \'activity\' => $schedule_row[\'activity\'],
                    \'location\' => $schedule_row[\'location\']
                ];
            }
        }
        
        // Get related events for this event
        $related_events = [];
        $related_result = $conn->query("SELECT related_event_id FROM event_related WHERE event_id = " . $row[\'id\']);
        
        if ($related_result->num_rows > 0) {
            while ($related_row = $related_result->fetch_assoc()) {
                $related_events[] = (int)$related_row[\'related_event_id\'];
            }
        }
        
        // Add event to array
        $events[] = [
            \'id\' => (int)$row[\'id\'],
            \'title\' => $row[\'title\'],
            \'category\' => $row[\'category\'],
            \'categoryClass\' => $row[\'category_class\'],
            \'status\' => $row[\'status\'],
            \'statusClass\' => $row[\'status_class\'],
            \'description\' => $row[\'description\'],
            \'date\' => $row[\'date\'],
            \'time\' => $row[\'time\'],
            \'location\' => $row[\'location\'],
            \'organizer\' => $row[\'organizer\'],
            \'image\' => $row[\'image\'],
            \'registrationDeadline\' => $row[\'registration_deadline\'],
            \'availableSeats\' => (int)$row[\'available_seats\'],
            \'contactEmail\' => $row[\'contact_email\'],
            \'contactPhone\' => $row[\'contact_phone\'],
            \'schedule\' => $schedule,
            \'relatedEvents\' => $related_events
        ];
    }
}

// Close connection
$conn->close();

// Return events as JSON
echo json_encode($events);
?>';

// Save the updated events.php file
file_put_contents('api/events.php', $newEventsPhp);

// 2. Now, let's update the event-details.js file
$eventDetailsJs = '// Event Details JavaScript for Campus Connect Portal

document.addEventListener(\'DOMContentLoaded\', function() {
    // Get event ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = parseInt(urlParams.get(\'id\'));
    
    if (!eventId) {
        showError("Event ID not found in URL");
        return;
    }
    
    // Show loading spinner
    document.getElementById(\'loading\').style.display = \'block\';
    
    // Fetch event from API
    fetch(`api/events.php?id=${eventId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }
            return response.json();
        })
        .then(events => {
            // Hide loading spinner
            document.getElementById(\'loading\').style.display = \'none\';
            
            if (!events || events.length === 0) {
                throw new Error("Event not found in API");
            }
            
            // Get the first (and should be only) event
            const event = events[0];
            
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
                    document.getElementById(\'loading\').style.display = \'none\';
                    
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
function loadRelatedEvents(relatedEventIds) {
    if (!relatedEventIds || relatedEventIds.length === 0) return;
    
    // Show related events section
    document.getElementById(\'relatedEventsSection\').style.display = \'block\';
    
    // Get container
    const container = document.getElementById(\'relatedEventsContainer\');
    
    // Fetch all events to find related ones
    fetch(\'api/events.php\')
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
                        document.getElementById(\'relatedEventsSection\').style.display = \'none\';
                    }
                } else {
                    document.getElementById(\'relatedEventsSection\').style.display = \'none\';
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
                    document.getElementById(\'relatedEventsSection\').style.display = \'none\';
                }
            } else {
                document.getElementById(\'relatedEventsSection\').style.display = \'none\';
            }
        });
}

// Display related events
function displayRelatedEvents(relatedEvents, container) {
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

// Save the updated event-details.js file
file_put_contents('event-details.js', $eventDetailsJs);

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

// 4. Now, let's fix the clubs.html page to properly display clubs from the API
$clubsJs = '// Clubs JavaScript for Campus Connect Portal

document.addEventListener("DOMContentLoaded", function() {
    // Initialize filter dropdown
    const clubCategoryFilter = document.getElementById("clubCategoryFilter");
    if (clubCategoryFilter) {
        clubCategoryFilter.addEventListener("change", filterClubs);
    }
    
    // Initialize search input
    const clubSearch = document.getElementById("clubSearch");
    if (clubSearch) {
        clubSearch.addEventListener("keyup", filterClubs);
    }
    
    // Load clubs from API
    loadClubs();
    
    // Initialize join club modal
    initJoinClubModal();
});

// Load clubs from API
function loadClubs() {
    const clubsContainer = document.getElementById("clubsContainer");
    
    if (clubsContainer) {
        // Clear container and show loading
        clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading clubs...</p></div>\';
        
        // Fetch clubs from API
        fetch("api/clubs.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`API request failed with status ${response.status}`);
                }
                return response.json();
            })
            .then(clubs => {
                console.log("Clubs loaded from API:", clubs);
                
                // Clear container
                clubsContainer.innerHTML = "";
                
                if (clubs && clubs.length > 0) {
                    // Add clubs
                    clubs.forEach(club => {
                        const clubCard = document.createElement("div");
                        clubCard.className = "col-lg-6 mb-4";
                        clubCard.setAttribute("data-category", club.category.toLowerCase());
                        
                        clubCard.innerHTML = `
                            <div class="card h-100">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="${club.image}" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="${club.name}"
                                             onerror="this.onerror=null; this.src=\'https://source.unsplash.com/300x400/?${club.category.toLowerCase()},club\'; console.log(\'Image failed to load, using fallback for: \' + this.alt);">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h4 class="card-title">${club.name}</h4>
                                                <span class="badge bg-${club.categoryClass}">${club.category}</span>
                                            </div>
                                            <p class="card-text">${club.description}</p>
                                            <div class="mb-3">
                                                <h6>Meeting Schedule:</h6>
                                                <p class="mb-0"><i class="fas fa-calendar-alt me-2"></i> ${club.meetingSchedule.days}</p>
                                                <p class="mb-0"><i class="fas fa-clock me-2"></i> ${club.meetingSchedule.time}</p>
                                                <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> ${club.meetingSchedule.location}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-users me-1"></i> ${club.members} members</span>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinClubModal" data-club="${club.name}">Join Club</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        clubsContainer.appendChild(clubCard);
                    });
                    
                    // Apply current filters
                    filterClubs();
                } else {
                    clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>No clubs found</h4><p>There are currently no clubs available.</p></div>\';
                }
            })
            .catch(error => {
                console.error("Error loading clubs from API:", error);
                
                // Try to load from static data as fallback
                if (typeof clubsData !== "undefined") {
                    console.log("Loading clubs from static data");
                    
                    // Clear container
                    clubsContainer.innerHTML = "";
                    
                    // Add clubs from static data
                    clubsData.forEach(club => {
                        const clubCard = document.createElement("div");
                        clubCard.className = "col-lg-6 mb-4";
                        clubCard.setAttribute("data-category", club.category.toLowerCase());
                        
                        clubCard.innerHTML = `
                            <div class="card h-100">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="${club.image}" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="${club.name}"
                                             onerror="this.onerror=null; this.src=\'https://source.unsplash.com/300x400/?${club.category.toLowerCase()},club\'; console.log(\'Image failed to load, using fallback for: \' + this.alt);">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h4 class="card-title">${club.name}</h4>
                                                <span class="badge bg-${club.categoryClass}">${club.category}</span>
                                            </div>
                                            <p class="card-text">${club.description}</p>
                                            <div class="mb-3">
                                                <h6>Meeting Schedule:</h6>
                                                <p class="mb-0"><i class="fas fa-calendar-alt me-2"></i> ${club.meetingSchedule.days}</p>
                                                <p class="mb-0"><i class="fas fa-clock me-2"></i> ${club.meetingSchedule.time}</p>
                                                <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> ${club.meetingSchedule.location}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-users me-1"></i> ${club.members} members</span>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinClubModal" data-club="${club.name}">Join Club</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        clubsContainer.appendChild(clubCard);
                    });
                    
                    // Apply current filters
                    filterClubs();
                } else {
                    clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>Error loading clubs</h4><p>Please try again later.</p></div>\';
                }
            });
    }
}

// Filter clubs
function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    console.log("Filtering clubs - Category:", categoryValue, "Search:", searchValue);
    
    const clubCards = document.querySelectorAll("#clubsContainer > div");
    let found = false;
    
    clubCards.forEach(card => {
        // Get the category from the badge text, not from data attribute
        const categoryBadge = card.querySelector(".badge");
        const category = categoryBadge ? categoryBadge.textContent.toLowerCase() : "";
        const clubName = card.querySelector(".card-title").textContent.toLowerCase();
        const clubDescription = card.querySelector(".card-text").textContent.toLowerCase();
        
        console.log("Club:", clubName, "Category:", category);
        
        let matchCategory = false;
        if (categoryValue === "all") {
            matchCategory = true;
        } else if (categoryValue === "tech" && category === "technology") {
            matchCategory = true;
        } else if (categoryValue === "social" && category === "social service") {
            matchCategory = true;
        } else if (category === categoryValue) {
            matchCategory = true;
        }
        
        const matchSearch = searchValue === "" || 
                          clubName.includes(searchValue) || 
                          clubDescription.includes(searchValue);
        
        if (matchCategory && matchSearch) {
            card.style.display = "block";
            found = true;
        } else {
            card.style.display = "none";
        }
    });
    
    // Show message if no clubs found
    const noResultsMsg = document.getElementById("noClubsMsg");
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement("div");
            msg.id = "noClubsMsg";
            msg.className = "col-12 text-center my-5";
            msg.innerHTML = `<h4>No clubs found matching your filters</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetClubFilters()">Reset Filters</button>`;
            document.getElementById("clubsContainer").appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset club filters
function resetClubFilters() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    if (categoryFilter) {
        categoryFilter.value = "all";
    }
    
    const searchFilter = document.getElementById("clubSearch");
    if (searchFilter) {
        searchFilter.value = "";
    }
    
    filterClubs();
}

// Initialize join club modal
function initJoinClubModal() {
    const joinClubModal = document.getElementById("joinClubModal");
    if (!joinClubModal) return;
    
    joinClubModal.addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget;
        const clubName = button.getAttribute("data-club");
        const clubNameElement = document.getElementById("clubName");
        
        if (clubNameElement) {
            clubNameElement.textContent = clubName;
        }
    });
    
    const submitJoinRequest = document.getElementById("submitJoinRequest");
    if (submitJoinRequest) {
        submitJoinRequest.addEventListener("click", function() {
            const form = document.getElementById("joinClubForm");
            
            // Simple form validation
            const inputs = form.querySelectorAll("input, textarea");
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.hasAttribute("required") && !input.value.trim()) {
                    input.classList.add("is-invalid");
                    isValid = false;
                } else {
                    input.classList.remove("is-invalid");
                }
            });
            
            if (isValid) {
                // Hide join club modal
                const joinClubModalInstance = bootstrap.Modal.getInstance(joinClubModal);
                joinClubModalInstance.hide();
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById("successModal"));
                successModal.show();
                
                // Reset form
                form.reset();
            }
        });
    }
}';

// Create a clubs.js file
file_put_contents('clubs.js', $clubsJs);

// 5. Update the clubs.html file to use the new clubs.js file
$clubsHtml = file_get_contents('clubs.html');

// Replace all script tags in clubs.html
$clubsHtml = preg_replace('/<script[\s\S]*?<\/script>/', '', $clubsHtml);

// Add our new scripts at the end of the body
$clubsScripts = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Clubs Data (Fallback) -->
    <script src="clubs-data.js"></script>
    <!-- API JS -->
    <script src="js/api.js"></script>
    <!-- Clubs JS -->
    <script src="clubs.js"></script>
';

// Add the scripts before the closing body tag
$clubsHtml = str_replace('</body>', $clubsScripts . '</body>', $clubsHtml);

// Save the updated clubs.html file
file_put_contents('clubs.html', $clubsHtml);

// 6. Update the database.php file to ensure proper error handling
$databasePhp = file_get_contents('config/database.php');

// Add better error handling to database.php
$newDatabasePhp = str_replace(
    '// Create database connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}',
    '// Create database connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}',
    $databasePhp
);

// Save the updated database.php file
file_put_contents('config/database.php', $newDatabasePhp);

echo "API integration fixed for both events and clubs!";
?>