<?php
// Fix script for filtering functionality on announcements and clubs pages

// 1. First, let's fix the announcements.html page
$announcementsHtml = file_get_contents('announcements.html');

// Create a completely new script section for announcements
$announcementsScript = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize filter buttons
    const filterButtons = document.querySelectorAll(".announcement-filter");
    
    filterButtons.forEach(button => {
        button.addEventListener("click", function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove("announcement-filter-active");
            });
            
            // Add active class to clicked button
            this.classList.add("announcement-filter-active");
            
            // Get filter value
            const filterValue = this.getAttribute("data-filter");
            
            // Filter announcements
            filterAnnouncements(filterValue);
        });
    });
    
    // Load announcements from API
    loadAnnouncements();
});

// Filter announcements
function filterAnnouncements(category) {
    const announcements = document.querySelectorAll(".announcement-card");
    let found = false;
    
    announcements.forEach(announcement => {
        if (category === "all") {
            announcement.style.display = "block";
            found = true;
        } else if (category === "important" && announcement.classList.contains("important")) {
            announcement.style.display = "block";
            found = true;
        } else if (announcement.classList.contains(category)) {
            announcement.style.display = "block";
            found = true;
        } else {
            announcement.style.display = "none";
        }
    });
    
    // Show message if no announcements found
    const noResultsMsg = document.getElementById("noAnnouncementsMsg");
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement("div");
            msg.id = "noAnnouncementsMsg";
            msg.className = "text-center my-5";
            msg.innerHTML = `<h4>No ${category} announcements found</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetAnnouncementFilters()">Show All Announcements</button>`;
            document.getElementById("announcementsContainer").appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset announcement filters
function resetAnnouncementFilters() {
    const filterButtons = document.querySelectorAll(".announcement-filter");
    
    // Remove active class from all buttons
    filterButtons.forEach(btn => {
        btn.classList.remove("announcement-filter-active");
    });
    
    // Add active class to "All" button
    const allButton = document.querySelector(".announcement-filter[data-filter=\'all\']");
    if (allButton) {
        allButton.classList.add("announcement-filter-active");
    }
    
    // Show all announcements
    filterAnnouncements("all");
}

// Load announcements from API
function loadAnnouncements() {
    const announcementsContainer = document.getElementById("announcementsContainer");
    
    if (announcementsContainer) {
        // Clear container and show loading
        announcementsContainer.innerHTML = \'<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading announcements...</p></div>\';
        
        // Fetch announcements from API
        fetch("api/announcements.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Failed to fetch announcements");
                }
                return response.json();
            })
            .then(announcements => {
                // Clear container
                announcementsContainer.innerHTML = "";
                
                if (announcements && announcements.length > 0) {
                    // Add announcements
                    announcements.forEach(announcement => {
                        // Determine CSS classes for filtering
                        let categoryClass = announcement.category.toLowerCase();
                        let priorityClass = announcement.priority === "high" ? "important" : "";
                        
                        const announcementCard = document.createElement("div");
                        announcementCard.className = `announcement-card mb-4 ${categoryClass} ${priorityClass}`;
                        
                        announcementCard.innerHTML = `
                            <div class="card ${announcement.priority === "high" ? "border-danger" : ""}">
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
                                        ${announcement.priority === "high" ? \'<span class="badge bg-danger">Important</span>\' : ""}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        announcementsContainer.appendChild(announcementCard);
                    });
                    
                    // Apply current filter if any
                    const activeFilter = document.querySelector(".announcement-filter-active");
                    if (activeFilter) {
                        filterAnnouncements(activeFilter.getAttribute("data-filter"));
                    }
                } else {
                    announcementsContainer.innerHTML = \'<div class="text-center my-5"><h4>No announcements found</h4><p>There are currently no announcements available.</p></div>\';
                }
            })
            .catch(error => {
                console.error("Error loading announcements:", error);
                
                // Try to load from static data as fallback
                if (typeof announcementsData !== "undefined") {
                    console.log("Loading announcements from static data");
                    
                    // Clear container
                    announcementsContainer.innerHTML = "";
                    
                    // Add announcements from static data
                    announcementsData.forEach(announcement => {
                        // Determine CSS classes for filtering
                        let categoryClass = announcement.category.toLowerCase();
                        let priorityClass = announcement.priority === "high" ? "important" : "";
                        
                        const announcementCard = document.createElement("div");
                        announcementCard.className = `announcement-card mb-4 ${categoryClass} ${priorityClass}`;
                        
                        announcementCard.innerHTML = `
                            <div class="card ${announcement.priority === "high" ? "border-danger" : ""}">
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
                                        ${announcement.priority === "high" ? \'<span class="badge bg-danger">Important</span>\' : ""}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        announcementsContainer.appendChild(announcementCard);
                    });
                    
                    // Apply current filter if any
                    const activeFilter = document.querySelector(".announcement-filter-active");
                    if (activeFilter) {
                        filterAnnouncements(activeFilter.getAttribute("data-filter"));
                    }
                } else {
                    announcementsContainer.innerHTML = \'<div class="text-center my-5"><h4>Error loading announcements</h4><p>Please try again later.</p></div>\';
                }
            });
    }
}
</script>
';

// Replace all script tags in announcements.html
$announcementsHtml = preg_replace('/<script[\s\S]*?<\/script>/', '', $announcementsHtml);

// Add our new script at the end of the body
$announcementsHtml = str_replace('</body>', $announcementsScript . '</body>', $announcementsHtml);

// Add the script tags for required libraries
$announcementsLibs = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Announcements Data (Fallback) -->
    <script src="announcements-data.js"></script>
';

// Add the libraries before our custom script
$announcementsHtml = str_replace($announcementsScript, $announcementsLibs . $announcementsScript, $announcementsHtml);

// Save the updated announcements.html
file_put_contents('announcements.html', $announcementsHtml);

// 2. Now, let's fix the clubs.html page
$clubsHtml = file_get_contents('clubs.html');

// Create a completely new script section for clubs
$clubsScript = '
<script>
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
});

// Filter clubs
function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    const clubCards = document.querySelectorAll("#clubsContainer > div");
    let found = false;
    
    clubCards.forEach(card => {
        const category = card.getAttribute("data-category");
        const clubName = card.querySelector(".card-title").textContent.toLowerCase();
        const clubDescription = card.querySelector(".card-text").textContent.toLowerCase();
        
        const matchCategory = categoryValue === "all" || category === categoryValue;
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
                    throw new Error("Failed to fetch clubs");
                }
                return response.json();
            })
            .then(clubs => {
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
                console.error("Error loading clubs:", error);
                
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

// Initialize join club modal
document.addEventListener("DOMContentLoaded", function() {
    const joinClubModal = document.getElementById("joinClubModal");
    if (joinClubModal) {
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
    }
});
</script>
';

// Replace all script tags in clubs.html
$clubsHtml = preg_replace('/<script[\s\S]*?<\/script>/', '', $clubsHtml);

// Add our new script at the end of the body
$clubsHtml = str_replace('</body>', $clubsScript . '</body>', $clubsHtml);

// Add the script tags for required libraries
$clubsLibs = '
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Clubs Data (Fallback) -->
    <script src="clubs-data.js"></script>
';

// Add the libraries before our custom script
$clubsHtml = str_replace($clubsScript, $clubsLibs . $clubsScript, $clubsHtml);

// Save the updated clubs.html
file_put_contents('clubs.html', $clubsHtml);

echo "Filtering functionality fixed for both announcements and clubs pages!";
?>