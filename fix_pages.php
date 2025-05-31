<?php
// Fix script for Campus Connect pages

// Fix events.html
$eventsHtml = file_get_contents('events.html');
$eventsHtml = preg_replace('/<\/body>\s*<\/html><!DOCTYPE html>[\s\S]*$/', '</body></html>', $eventsHtml);

// Update events.html to use API
$scriptSection = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Events Data (Fallback) -->
    <script src="events-data.js"></script>
    <!-- API JS -->
    <script src="js/api.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
    <!-- Events Page Specific JS -->
    <script>
        document.addEventListener(\'DOMContentLoaded\', function() {
            // Load events from API
            const eventsContainer = document.getElementById(\'eventsContainer\');
            
            if (eventsContainer) {
                // Clear container and show loading
                eventsContainer.innerHTML = \'<div class="col-12 text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading events...</p></div>\';
                
                // Fetch events from API
                fetchEvents().then(events => {
                    // Clear container
                    eventsContainer.innerHTML = \'\';
                    
                    if (events && events.length > 0) {
                        // Add events
                        events.forEach(event => {
                            const eventCard = document.createElement(\'div\');
                            eventCard.className = \'col-md-4 mb-4\';
                            eventCard.dataset.category = event.category.toLowerCase();
                            eventCard.dataset.date = \'upcoming\'; // This would be dynamic in a real app
                            
                            // Log the image path for debugging
                            console.log(\'Event image path:\', event.image);
                            
                            eventCard.innerHTML = `
                                <div class="card h-100">
                                    <img src="${event.image}" 
                                         class="card-img-top" alt="${event.title}" 
                                         style="height: 220px; object-fit: cover;"
                                         onerror="this.onerror=null; this.src=\'https://source.unsplash.com/600x400/?${event.category.toLowerCase()},event\'; console.log(\'Image failed to load, using fallback for: \' + this.alt);">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">${event.title}</h5>
                                            <span class="badge bg-${event.categoryClass}">${event.category}</span>
                                        </div>
                                        <p class="card-text">${event.description.substring(0, 100)}...</p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt"></i> ${event.date} | 
                                                <i class="fas fa-map-marker-alt"></i> ${event.location}
                                            </small>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="event-details.html?id=${event.id}" class="btn btn-accent">View Details</a>
                                            <span class="badge bg-${event.statusClass}">${event.status}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            eventsContainer.appendChild(eventCard);
                        });
                    } else {
                        eventsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>No events found</h4><p>There are currently no events available.</p></div>\';
                    }
                }).catch(error => {
                    console.error(\'Error loading events:\', error);
                    eventsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>Error loading events</h4><p>Please try again later.</p></div>\';
                });
            } else {
                console.error(\'Events container not found\');
            }
        });
    </script>
';

// Replace the script section in events.html
$pattern = '/<\!-- Bootstrap JS Bundle with Popper --\>[\s\S]*?<\/script>/';
$eventsHtml = preg_replace($pattern, $scriptSection, $eventsHtml);
file_put_contents('events.html', $eventsHtml);

// Fix clubs.html
$clubsHtml = file_get_contents('clubs.html');
$clubsHtml = preg_replace('/<\/body>\s*<\/html><!DOCTYPE html>[\s\S]*$/', '</body></html>', $clubsHtml);

// Update clubs.html to use API
$clubsScriptSection = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Clubs Data (Fallback) -->
    <script src="clubs-data.js"></script>
    <!-- API JS -->
    <script src="js/api.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
    <!-- Clubs Page Specific JS -->
    <script>
        document.addEventListener(\'DOMContentLoaded\', function() {
            // Load clubs from API
            const clubsContainer = document.getElementById(\'clubsContainer\');
            
            if (clubsContainer) {
                // Clear container and show loading
                clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading clubs...</p></div>\';
                
                // Fetch clubs from API
                fetchClubs().then(clubs => {
                    // Clear container
                    clubsContainer.innerHTML = \'\';
                    
                    if (clubs && clubs.length > 0) {
                        // Add clubs
                        clubs.forEach(club => {
                            const clubCard = document.createElement(\'div\');
                            clubCard.className = \'col-lg-6 mb-4\';
                            clubCard.dataset.category = club.category.toLowerCase();
                            
                            clubCard.innerHTML = `
                                <div class="card club-card h-100">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="${club.image}" class="img-fluid rounded-start h-100" alt="${club.name}" style="object-fit: cover;"
                                                 onerror="this.onerror=null; this.src=\'https://source.unsplash.com/300x400/?${club.category.toLowerCase()},club\'; console.log(\'Image failed to load, using fallback for: \' + this.alt);">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body d-flex flex-column h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title">${club.name}</h5>
                                                    <span class="badge bg-${club.categoryClass}">${club.category}</span>
                                                </div>
                                                <p class="card-text">${club.description}</p>
                                                <div class="mt-auto">
                                                    <p class="card-text mb-1">
                                                        <small class="text-muted">
                                                            <i class="fas fa-users"></i> ${club.members} members
                                                        </small>
                                                    </p>
                                                    <p class="card-text mb-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt"></i> ${club.meetingSchedule.days} | 
                                                            <i class="fas fa-clock"></i> ${club.meetingSchedule.time} | 
                                                            <i class="fas fa-map-marker-alt"></i> ${club.meetingSchedule.location}
                                                        </small>
                                                    </p>
                                                    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#joinClubModal" data-club="${club.name}">Join Club</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            clubsContainer.appendChild(clubCard);
                        });
                    } else {
                        clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>No clubs found</h4><p>There are currently no clubs available.</p></div>\';
                    }
                }).catch(error => {
                    console.error(\'Error loading clubs:\', error);
                    clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>Error loading clubs</h4><p>Please try again later.</p></div>\';
                });
            } else {
                console.error(\'Clubs container not found\');
            }
        });
    </script>
';

// Replace the script section in clubs.html
$pattern = '/<\!-- Bootstrap JS Bundle with Popper --\>[\s\S]*?<\/script>/';
$clubsHtml = preg_replace($pattern, $clubsScriptSection, $clubsHtml);
file_put_contents('clubs.html', $clubsHtml);

// Fix announcements.html
$announcementsHtml = file_get_contents('announcements.html');
$announcementsHtml = preg_replace('/<\/body>\s*<\/html><!DOCTYPE html>[\s\S]*$/', '</body></html>', $announcementsHtml);

// Update announcements.html to use API
$announcementsScriptSection = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Announcements Data (Fallback) -->
    <script src="announcements-data.js"></script>
    <!-- API JS -->
    <script src="js/api.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
    <!-- Announcements Page Specific JS -->
    <script>
        document.addEventListener(\'DOMContentLoaded\', function() {
            // Load announcements from API
            const announcementsContainer = document.getElementById(\'announcementsContainer\');
            
            if (announcementsContainer) {
                // Clear container and show loading
                announcementsContainer.innerHTML = \'<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading announcements...</p></div>\';
                
                // Fetch announcements from API
                fetchAnnouncements().then(announcements => {
                    // Clear container
                    announcementsContainer.innerHTML = \'\';
                    
                    if (announcements && announcements.length > 0) {
                        // Add announcements
                        announcements.forEach(announcement => {
                            const announcementCard = document.createElement(\'div\');
                            announcementCard.className = \'announcement-card mb-4\';
                            announcementCard.dataset.category = announcement.category.toLowerCase();
                            announcementCard.dataset.priority = announcement.priority.toLowerCase();
                            
                            announcementCard.innerHTML = `
                                <div class="card ${announcement.priority === \'high\' ? \'border-danger\' : \'\'}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="announcement-title mb-0">${announcement.title}</h5>
                                        <span class="badge bg-${announcement.categoryClass}">${announcement.category}</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="announcement-content">${announcement.content}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> ${announcement.author} | 
                                                <i class="fas fa-calendar-alt"></i> ${announcement.displayDate || announcement.date}
                                            </small>
                                            ${announcement.priority === \'high\' ? \'<span class="badge bg-danger">Important</span>\' : \'\'}
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            announcementsContainer.appendChild(announcementCard);
                        });
                    } else {
                        announcementsContainer.innerHTML = \'<div class="text-center my-5"><h4>No announcements found</h4><p>There are currently no announcements available.</p></div>\';
                    }
                }).catch(error => {
                    console.error(\'Error loading announcements:\', error);
                    announcementsContainer.innerHTML = \'<div class="text-center my-5"><h4>Error loading announcements</h4><p>Please try again later.</p></div>\';
                });
            } else {
                console.error(\'Announcements container not found\');
            }
        });
    </script>
';

// Replace the script section in announcements.html
$pattern = '/<\!-- Bootstrap JS Bundle with Popper --\>[\s\S]*?<\/script>/';
$announcementsHtml = preg_replace($pattern, $announcementsScriptSection, $announcementsHtml);
file_put_contents('announcements.html', $announcementsHtml);

echo "Pages fixed successfully!";
?>