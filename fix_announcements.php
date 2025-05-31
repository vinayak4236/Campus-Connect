<?php
// Fix script for announcements page

// Update the announcements.html file
$announcementsHtml = file_get_contents('announcements.html');

// Update the script section to properly handle categories
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
            // Initialize announcement filters
            initAnnouncementFilters();
            
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
                            // Determine CSS classes for filtering
                            let categoryClass = announcement.category.toLowerCase();
                            let priorityClass = announcement.priority === \'high\' ? \'important\' : \'\';
                            
                            const announcementCard = document.createElement(\'div\');
                            announcementCard.className = `announcement-card mb-4 ${categoryClass} ${priorityClass}`;
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
                        
                        // Apply current filter if any
                        const activeFilter = document.querySelector(\'.announcement-filter-active\');
                        if (activeFilter) {
                            filterAnnouncements(activeFilter.getAttribute(\'data-filter\'));
                        }
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

// Update the filterAnnouncements function in script.js
$scriptJs = file_get_contents('script.js');

$newFilterFunction = '
// Filter announcements
function filterAnnouncements(category) {
    const announcements = document.querySelectorAll(\'.announcement-card\');
    let found = false;
    
    if (category === \'all\') {
        announcements.forEach(announcement => {
            announcement.style.display = \'block\';
        });
        return;
    }
    
    announcements.forEach(announcement => {
        if (announcement.classList.contains(category)) {
            announcement.style.display = \'block\';
            found = true;
        } else {
            announcement.style.display = \'none\';
        }
    });
    
    // Show message if no announcements found
    const noResultsMsg = document.getElementById(\'noAnnouncementsMsg\');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement(\'div\');
            msg.id = \'noAnnouncementsMsg\';
            msg.className = \'text-center my-5\';
            msg.innerHTML = `<h4>No ${category} announcements found</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetAnnouncementFilters()">Show All Announcements</button>`;
            document.getElementById(\'announcementsContainer\').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset announcement filters
function resetAnnouncementFilters() {
    const filterButtons = document.querySelectorAll(\'.announcement-filter\');
    
    // Remove active class from all buttons
    filterButtons.forEach(btn => {
        btn.classList.remove(\'announcement-filter-active\');
    });
    
    // Add active class to "All" button
    const allButton = document.querySelector(\'.announcement-filter[data-filter="all"]\');
    if (allButton) {
        allButton.classList.add(\'announcement-filter-active\');
    }
    
    // Show all announcements
    filterAnnouncements(\'all\');
}
';

// Replace the filterAnnouncements function in script.js
$pattern = '/\/\/ Filter announcements\s*function filterAnnouncements\(category\) \{[\s\S]*?\}/';
$scriptJs = preg_replace($pattern, $newFilterFunction, $scriptJs);
file_put_contents('script.js', $scriptJs);

echo "Announcements page fixed successfully!";
?>