// Clubs JavaScript for Campus Connect Portal

document.addEventListener("DOMContentLoaded", function() {
    console.log("Clubs page loaded");
    
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
        clubsContainer.innerHTML = '<div class="col-12 text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading clubs...</p></div>';
        
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
                        const clubCard = createClubCard(club);
                        clubsContainer.appendChild(clubCard);
                    });
                    
                    // Apply current filters
                    filterClubs();
                } else {
                    clubsContainer.innerHTML = '<div class="col-12 text-center my-5"><h4>No clubs found</h4><p>There are currently no clubs available.</p></div>';
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
                        const clubCard = createClubCard(club);
                        clubsContainer.appendChild(clubCard);
                    });
                    
                    // Apply current filters
                    filterClubs();
                } else {
                    clubsContainer.innerHTML = '<div class="col-12 text-center my-5"><h4>Error loading clubs</h4><p>Please try again later.</p></div>';
                }
            });
    }
}

// Create a club card element
function createClubCard(club) {
    const clubCard = document.createElement("div");
    clubCard.className = "col-md-6 col-lg-4 mb-4";
    clubCard.setAttribute("data-category", club.category.toLowerCase());
    
    clubCard.innerHTML = `
        <div class="card club-card h-100 shadow-sm">
            <div class="card-img-container">
                <img src="${club.image}" class="card-img-top" alt="${club.name}"
                     onerror="this.onerror=null; this.src='https://source.unsplash.com/300x200/?${club.category.toLowerCase()},club'; console.log('Image failed to load, using fallback for: ' + this.alt);">
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title">${club.name}</h5>
                    <span class="badge bg-${club.categoryClass}">${club.category}</span>
                </div>
                <p class="card-text">${club.description}</p>
                <div class="club-info mt-3">
                    <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i> ${club.meetingSchedule.days}</p>
                    <p class="mb-1"><i class="fas fa-clock me-2"></i> ${club.meetingSchedule.time}</p>
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i> ${club.meetingSchedule.location}</p>
                    <p class="mb-3"><i class="fas fa-users me-2"></i> ${club.members} members</p>
                </div>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#joinClubModal" data-club="${club.name}">Join Club</button>
            </div>
        </div>
    `;
    
    return clubCard;
}

// Filter clubs
function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    console.log("Filtering clubs - Category:", categoryValue, "Search:", searchValue);
    
    const clubCards = document.querySelectorAll("#clubsContainer > div[data-category]");
    let found = false;
    
    clubCards.forEach(card => {
        const category = card.getAttribute("data-category");
        const clubName = card.querySelector(".card-title").textContent.toLowerCase();
        const clubDescription = card.querySelector(".card-text").textContent.toLowerCase();
        
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
            card.style.display = "";
            found = true;
        } else {
            card.style.display = "none";
        }
    });
    
    // Show message if no clubs found
    const noResultsMsg = document.getElementById("noClubsMsg");
    if (!found && clubCards.length > 0) {
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
}
