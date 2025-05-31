// Main JavaScript file for Campus Connect Portal

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initSearch();
    
    // Initialize filter functionality
    initFilters();
    
    // Initialize join club modal
    initJoinClubModal();
    
    // Initialize announcement filters if on announcements page
    if (window.location.pathname.includes('announcements.html')) {
        initAnnouncementFilters();
    }
    
    // Load featured content on home page
    if (window.location.pathname.includes('index.html') || window.location.pathname.endsWith('/')) {
        loadFeaturedEvents();
        loadFeaturedClubs();
        loadFeaturedAnnouncements();
    }
});

// Search functionality
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('keyup', function(e) {
        // If Enter key is pressed
        if (e.key === 'Enter') {
            e.preventDefault();
            
            const searchTerm = searchInput.value.toLowerCase().trim();
            
            // If on events page
            if (window.location.pathname.includes('events.html')) {
                searchEvents(searchTerm);
            }
            // If on clubs page
            else if (window.location.pathname.includes('clubs.html')) {
                searchClubs(searchTerm);
            }
            // If on announcements page
            else if (window.location.pathname.includes('announcements.html')) {
                searchAnnouncements(searchTerm);
            }
            // If on home page, redirect to search results page (not implemented)
            else {
                alert('Search functionality: "' + searchTerm + '" would search across all content.');
            }
        }
    });
}

// Search events
function searchEvents(term) {
    const eventCards = document.querySelectorAll('#eventsContainer .col-md-4');
    let found = false;
    
    eventCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        const category = card.querySelector('.badge').textContent.toLowerCase();
        
        if (title.includes(term) || description.includes(term) || category.includes(term)) {
            card.style.display = 'block';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show message if no events found
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'col-12 text-center my-5';
            msg.innerHTML = `<h4>No events found matching "${term}"</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetSearch()">Show All Events</button>`;
            document.getElementById('eventsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Search clubs
function searchClubs(term) {
    const clubCards = document.querySelectorAll('#clubsContainer .col-lg-6');
    let found = false;
    
    clubCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        const category = card.querySelector('.badge').textContent.toLowerCase();
        
        if (title.includes(term) || description.includes(term) || category.includes(term)) {
            card.style.display = 'block';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show message if no clubs found
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'col-12 text-center my-5';
            msg.innerHTML = `<h4>No clubs found matching "${term}"</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetSearch()">Show All Clubs</button>`;
            document.getElementById('clubsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Search announcements
function searchAnnouncements(term) {
    const announcements = document.querySelectorAll('.announcement-card');
    let found = false;
    
    announcements.forEach(announcement => {
        const title = announcement.querySelector('.announcement-title').textContent.toLowerCase();
        const content = announcement.querySelector('.announcement-content').textContent.toLowerCase();
        const category = announcement.querySelector('.badge').textContent.toLowerCase();
        
        if (title.includes(term) || content.includes(term) || category.includes(term)) {
            announcement.style.display = 'block';
            found = true;
        } else {
            announcement.style.display = 'none';
        }
    });
    
    // Show message if no announcements found
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'col-12 text-center my-5';
            msg.innerHTML = `<h4>No announcements found matching "${term}"</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetSearch()">Show All Announcements</button>`;
            document.getElementById('announcementsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset search
function resetSearch() {
    // Reset events
    if (window.location.pathname.includes('events.html')) {
        const eventCards = document.querySelectorAll('#eventsContainer .col-md-4');
        eventCards.forEach(card => {
            card.style.display = 'block';
        });
    }
    // Reset clubs
    else if (window.location.pathname.includes('clubs.html')) {
        const clubCards = document.querySelectorAll('#clubsContainer .col-lg-6');
        clubCards.forEach(card => {
            card.style.display = 'block';
        });
    }
    // Reset announcements
    else if (window.location.pathname.includes('announcements.html')) {
        const announcements = document.querySelectorAll('.announcement-card');
        announcements.forEach(announcement => {
            announcement.style.display = 'block';
        });
    }
    
    // Remove no results message
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (noResultsMsg) {
        noResultsMsg.remove();
    }
    
    // Reset search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset filters
    const categoryFilter = document.getElementById('categoryFilter') || 
                          document.getElementById('clubCategoryFilter') ||
                          document.getElementById('announcementCategoryFilter');
    if (categoryFilter) {
        categoryFilter.value = 'all';
    }
    
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.value = 'all';
    }
}

// Initialize filters
function initFilters() {
    // Event category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterEvents();
        });
    }
    
    // Event date filter
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            filterEvents();
        });
    }
    
    // Club category filter
    const clubCategoryFilter = document.getElementById('clubCategoryFilter');
    if (clubCategoryFilter) {
        clubCategoryFilter.addEventListener('change', function() {
            filterClubs();
        });
    }
    
    // Event search filter
    const eventSearch = document.getElementById('eventSearch');
    if (eventSearch) {
        eventSearch.addEventListener('keyup', function() {
            filterEvents();
        });
    }
    
    // Club search filter
    const clubSearch = document.getElementById('clubSearch');
    if (clubSearch) {
        clubSearch.addEventListener('keyup', function() {
            filterClubs();
        });
    }
}

// Filter events
function filterEvents() {
    const categoryFilter = document.getElementById('categoryFilter');
    const dateFilter = document.getElementById('dateFilter');
    const eventSearch = document.getElementById('eventSearch');
    
    const categoryValue = categoryFilter ? categoryFilter.value : 'all';
    const dateValue = dateFilter ? dateFilter.value : 'all';
    const searchValue = eventSearch ? eventSearch.value.toLowerCase().trim() : '';
    
    const eventCards = document.querySelectorAll('#eventsContainer .col-md-4');
    let found = false;
    
    eventCards.forEach(card => {
        const category = card.dataset.category;
        const date = card.dataset.date;
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        
        const matchCategory = categoryValue === 'all' || category === categoryValue;
        const matchDate = dateValue === 'all' || date === dateValue;
        const matchSearch = searchValue === '' || 
                          title.includes(searchValue) || 
                          description.includes(searchValue);
        
        if (matchCategory && matchDate && matchSearch) {
            card.style.display = 'block';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show message if no events found
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'col-12 text-center my-5';
            msg.innerHTML = `<h4>No events found matching your filters</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetSearch()">Reset Filters</button>`;
            document.getElementById('eventsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Filter clubs
function filterClubs() {
    const clubCategoryFilter = document.getElementById('clubCategoryFilter');
    const clubSearch = document.getElementById('clubSearch');
    
    const categoryValue = clubCategoryFilter ? clubCategoryFilter.value : 'all';
    const searchValue = clubSearch ? clubSearch.value.toLowerCase().trim() : '';
    
    const clubCards = document.querySelectorAll('#clubsContainer .col-lg-6');
    let found = false;
    
    clubCards.forEach(card => {
        const category = card.dataset.category;
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        
        const matchCategory = categoryValue === 'all' || category === categoryValue;
        const matchSearch = searchValue === '' || 
                          title.includes(searchValue) || 
                          description.includes(searchValue);
        
        if (matchCategory && matchSearch) {
            card.style.display = 'block';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show message if no clubs found
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'col-12 text-center my-5';
            msg.innerHTML = `<h4>No clubs found matching your filters</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetSearch()">Reset Filters</button>`;
            document.getElementById('clubsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Initialize join club modal
function initJoinClubModal() {
    const joinClubModal = document.getElementById('joinClubModal');
    if (!joinClubModal) return;
    
    joinClubModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const clubName = button.getAttribute('data-club');
        const clubNameElement = document.getElementById('clubName');
        
        clubNameElement.textContent = clubName;
    });
    
    const submitJoinRequest = document.getElementById('submitJoinRequest');
    if (submitJoinRequest) {
        submitJoinRequest.addEventListener('click', function() {
            const form = document.getElementById('joinClubForm');
            
            // Simple form validation
            const inputs = form.querySelectorAll('input, textarea');
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (isValid) {
                // Hide join club modal
                const joinClubModalInstance = bootstrap.Modal.getInstance(joinClubModal);
                joinClubModalInstance.hide();
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reset form
                form.reset();
            }
        });
    }
}

// Initialize announcement filters
function initAnnouncementFilters() {
    const filterButtons = document.querySelectorAll('.announcement-filter');
    if (!filterButtons.length) return;
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('announcement-filter-active');
            });
            
            // Add active class to clicked button
            this.classList.add('announcement-filter-active');
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Filter announcements
            filterAnnouncements(filterValue);
        });
    });
}


// Filter announcements
function filterAnnouncements(category) {
    const announcements = document.querySelectorAll('.announcement-card');
    let found = false;
    
    if (category === 'all') {
        announcements.forEach(announcement => {
            announcement.style.display = 'block';
        });
        return;
    }
    
    announcements.forEach(announcement => {
        if (announcement.classList.contains(category)) {
            announcement.style.display = 'block';
            found = true;
        } else {
            announcement.style.display = 'none';
        }
    });
    
    // Show message if no announcements found
    const noResultsMsg = document.getElementById('noAnnouncementsMsg');
    if (!found) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noAnnouncementsMsg';
            msg.className = 'text-center my-5';
            msg.innerHTML = `<h4>No ${category} announcements found</h4>
                            <button class="btn btn-outline-primary mt-3" onclick="resetAnnouncementFilters()">Show All Announcements</button>`;
            document.getElementById('announcementsContainer').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset announcement filters
function resetAnnouncementFilters() {
    const filterButtons = document.querySelectorAll('.announcement-filter');
    
    // Remove active class from all buttons
    filterButtons.forEach(btn => {
        btn.classList.remove('announcement-filter-active');
    });
    
    // Add active class to "All" button
    const allButton = document.querySelector('.announcement-filter[data-filter="all"]');
    if (allButton) {
        allButton.classList.add('announcement-filter-active');
    }
    
    // Show all announcements
    filterAnnouncements('all');
}
);
        return;
    }
    
    announcements.forEach(announcement => {
        if (announcement.classList.contains(category)) {
            announcement.style.display = 'block';
        } else {
            announcement.style.display = 'none';
        }
    });
}

// Load featured events on the home page
function loadFeaturedEvents() {
    const featuredEventsContainer = document.getElementById('featuredEventsContainer');
    if (!featuredEventsContainer) return;
    
    // Check if eventsData is available
    if (typeof eventsData === 'undefined') {
        // Load events-data.js dynamically
        const script = document.createElement('script');
        script.src = 'events-data.js';
        script.onload = function() {
            displayFeaturedEvents(featuredEventsContainer);
        };
        document.head.appendChild(script);
    } else {
        displayFeaturedEvents(featuredEventsContainer);
    }
}

// Display featured events
function displayFeaturedEvents(container) {
    // Clear container
    container.innerHTML = '';
    
    // Get the first 3 events (or fewer if there are less than 3)
    const featuredEvents = eventsData.slice(0, 3);
    
    // Add events
    featuredEvents.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'col-md-4 mb-4';
        
        eventCard.innerHTML = `
            <div class="card h-100">
                <img src="${event.image}" 
                     class="card-img-top" alt="${event.title}" 
                     style="height: 220px; object-fit: cover;"
                     onerror="this.onerror=null; this.src='https://source.unsplash.com/600x400/?${event.category.toLowerCase()},event';">
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
        
        container.appendChild(eventCard);
    });
}

// Load featured clubs on the home page
function loadFeaturedClubs() {
    const featuredClubsContainer = document.getElementById('featuredClubsContainer');
    if (!featuredClubsContainer) return;
    
    // Check if clubsData is available
    if (typeof clubsData === 'undefined') {
        // Load clubs-data.js dynamically
        const script = document.createElement('script');
        script.src = 'clubs-data.js';
        script.onload = function() {
            displayFeaturedClubs(featuredClubsContainer);
        };
        document.head.appendChild(script);
    } else {
        displayFeaturedClubs(featuredClubsContainer);
    }
}

// Display featured clubs
function displayFeaturedClubs(container) {
    // Clear container
    container.innerHTML = '';
    
    // Get the first 2 clubs (or fewer if there are less than 2)
    const featuredClubs = clubsData.slice(0, 2);
    
    // Add clubs
    featuredClubs.forEach(club => {
        const clubCard = document.createElement('div');
        clubCard.className = 'col-md-6 mb-4';
        
        clubCard.innerHTML = `
            <div class="card h-100">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="${club.image}" 
                             class="img-fluid rounded-start h-100 w-100 object-fit-cover" 
                             alt="${club.name}"
                             onerror="this.onerror=null; this.src='https://source.unsplash.com/600x400/?${club.category.toLowerCase()},club';">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title">${club.name}</h5>
                                <span class="badge bg-${club.categoryClass}">${club.category}</span>
                            </div>
                            <p class="card-text">${club.description.substring(0, 100)}...</p>
                            <a href="clubs.html#${club.id}" class="btn btn-sm btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(clubCard);
    });
}

// Load featured announcements on the home page
function loadFeaturedAnnouncements() {
    const featuredAnnouncementsContainer = document.getElementById('featuredAnnouncementsContainer');
    if (!featuredAnnouncementsContainer) return;
    
    // Check if announcementsData is available
    if (typeof announcementsData === 'undefined') {
        // Load announcements-data.js dynamically
        const script = document.createElement('script');
        script.src = 'announcements-data.js';
        script.onload = function() {
            displayFeaturedAnnouncements(featuredAnnouncementsContainer);
        };
        document.head.appendChild(script);
    } else {
        displayFeaturedAnnouncements(featuredAnnouncementsContainer);
    }
}

// Display featured announcements
function displayFeaturedAnnouncements(container) {
    // Clear container
    container.innerHTML = '';
    
    // Get the first 3 announcements (or fewer if there are less than 3)
    const featuredAnnouncements = announcementsData.slice(0, 3);
    
    // Add announcements
    featuredAnnouncements.forEach(announcement => {
        const announcementItem = document.createElement('div');
        announcementItem.className = 'list-group-item list-group-item-action';
        
        announcementItem.innerHTML = `
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">${announcement.title}</h5>
                <small class="text-muted">${announcement.displayDate}</small>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-${announcement.categoryClass}">${announcement.category}</span>
                <small class="text-muted">By: ${announcement.author}</small>
            </div>
            <p class="mb-1">${announcement.content.substring(0, 150)}${announcement.content.length > 150 ? '...' : ''}</p>
            <a href="announcements.html#announcement-${announcement.id}" class="btn btn-sm btn-outline-secondary mt-2">Read More</a>
        `;
        
        container.appendChild(announcementItem);
    });
}