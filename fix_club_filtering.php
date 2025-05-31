<?php
// Fix script for club filtering functionality

// Get the clubs.html content
$clubsHtml = file_get_contents('clubs.html');

// Create a new script to fix the filtering issue
$fixScript = '
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
    
    // Load clubs from API or static data
    loadClubs();
});

// Filter clubs
function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    console.log("Filtering by category:", categoryValue);
    
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

// Load clubs from API or static data
function loadClubs() {
    const clubsContainer = document.getElementById("clubsContainer");
    
    if (clubsContainer) {
        // If clubs are already loaded (static HTML), just apply filters
        if (clubsContainer.children.length > 0 && !clubsContainer.querySelector(".spinner-border")) {
            console.log("Clubs already loaded, applying filters");
            filterClubs();
            return;
        }
        
        // Clear container and show loading
        clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading clubs...</p></div>\';
        
        // Try to load from static data first (for simplicity in this fix)
        if (typeof clubsData !== "undefined") {
            console.log("Loading clubs from static data");
            
            // Clear container
            clubsContainer.innerHTML = "";
            
            // Add clubs from static data
            clubsData.forEach(club => {
                const clubCard = document.createElement("div");
                clubCard.className = "col-lg-6 mb-4";
                // Store the category in a data attribute for filtering
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
                    clubsContainer.innerHTML = \'<div class="col-12 text-center my-5"><h4>Error loading clubs</h4><p>Please try again later.</p></div>\';
                });
        }
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

// Add the required libraries
$requiredLibs = '
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Clubs Data -->
<script src="clubs-data.js"></script>
';

// Find the closing body tag
$bodyClosePos = strrpos($clubsHtml, '</body>');

// Insert our scripts before the closing body tag
if ($bodyClosePos !== false) {
    $clubsHtml = substr_replace($clubsHtml, $requiredLibs . $fixScript, $bodyClosePos, 0);
}

// Save the updated clubs.html
file_put_contents('clubs.html', $clubsHtml);

echo "Club filtering functionality fixed!";
?>