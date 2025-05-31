<?php
// Fix script for club card layout issues

// Update the clubs.js file to fix the layout issues
$clubsJs = file_get_contents('clubs.js');

// Update the loadClubs function to use a row wrapper for every 2 clubs
$newClubsJs = str_replace(
    '// Load clubs from API
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
                        clubCard.setAttribute("data-category", club.category.toLowerCase());',
    '// Load clubs from API
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
                    // Create rows for every 2 clubs
                    let currentRow = null;
                    
                    // Add clubs
                    clubs.forEach((club, index) => {
                        // Create a new row for every 2 clubs
                        if (index % 2 === 0) {
                            currentRow = document.createElement("div");
                            currentRow.className = "row";
                            clubsContainer.appendChild(currentRow);
                        }
                        
                        const clubCard = document.createElement("div");
                        clubCard.className = "col-lg-6 mb-4";
                        clubCard.setAttribute("data-category", club.category.toLowerCase());',
    $clubsJs
);

// Also update the fallback static data loading
$newClubsJs = str_replace(
    '// Try to load from static data as fallback
                if (typeof clubsData !== "undefined") {
                    console.log("Loading clubs from static data");
                    
                    // Clear container
                    clubsContainer.innerHTML = "";
                    
                    // Add clubs from static data
                    clubsData.forEach(club => {
                        const clubCard = document.createElement("div");
                        clubCard.className = "col-lg-6 mb-4";
                        clubCard.setAttribute("data-category", club.category.toLowerCase());',
    '// Try to load from static data as fallback
                if (typeof clubsData !== "undefined") {
                    console.log("Loading clubs from static data");
                    
                    // Clear container
                    clubsContainer.innerHTML = "";
                    
                    // Create rows for every 2 clubs
                    let currentRow = null;
                    
                    // Add clubs from static data
                    clubsData.forEach((club, index) => {
                        // Create a new row for every 2 clubs
                        if (index % 2 === 0) {
                            currentRow = document.createElement("div");
                            currentRow.className = "row";
                            clubsContainer.appendChild(currentRow);
                        }
                        
                        const clubCard = document.createElement("div");
                        clubCard.className = "col-lg-6 mb-4";
                        clubCard.setAttribute("data-category", club.category.toLowerCase());',
    $newClubsJs
);

// Update where the club cards are appended
$newClubsJs = str_replace(
    'clubsContainer.appendChild(clubCard);',
    'currentRow.appendChild(clubCard);',
    $newClubsJs
);

// Save the updated clubs.js file
file_put_contents('clubs.js', $newClubsJs);

// Update the clubs.html file to ensure proper structure
$clubsHtml = file_get_contents('clubs.html');

// Make sure the container has the proper structure
$newClubsHtml = str_replace(
    '<div class="row" id="clubsContainer">',
    '<div id="clubsContainer">',
    $clubsHtml
);

// Save the updated clubs.html file
file_put_contents('clubs.html', $newClubsHtml);

// Update the filterClubs function to handle the new structure
$newClubsJs = str_replace(
    'function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    console.log("Filtering clubs - Category:", categoryValue, "Search:", searchValue);
    
    const clubCards = document.querySelectorAll("#clubsContainer > div");
    let found = false;',
    'function filterClubs() {
    const categoryFilter = document.getElementById("clubCategoryFilter");
    const searchFilter = document.getElementById("clubSearch");
    
    const categoryValue = categoryFilter ? categoryFilter.value : "all";
    const searchValue = searchFilter ? searchFilter.value.toLowerCase().trim() : "";
    
    console.log("Filtering clubs - Category:", categoryValue, "Search:", searchValue);
    
    // Get all club cards (now they are inside row divs)
    const clubCards = document.querySelectorAll("#clubsContainer .col-lg-6");
    let found = false;',
    $newClubsJs
);

// Update the CSS to ensure proper display
$newClubsJs = str_replace(
    'card.style.display = "block";',
    'card.style.display = "";',
    $newClubsJs
);

// Save the updated clubs.js file again
file_put_contents('clubs.js', $newClubsJs);

// Create a CSS fix for any remaining layout issues
$cssFixContent = '
/* Fix for club card layout */
#clubsContainer .row {
    display: flex;
    flex-wrap: wrap;
}

#clubsContainer .col-lg-6 {
    display: flex;
}

#clubsContainer .card {
    width: 100%;
}

#clubsContainer .card .row {
    height: 100%;
}

#clubsContainer .card .col-md-4 {
    display: flex;
}

#clubsContainer .card .col-md-4 img {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

#clubsContainer .card .col-md-8 {
    display: flex;
    flex-direction: column;
}

#clubsContainer .card .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

#clubsContainer .card .card-body .d-flex.justify-content-between.align-items-center {
    margin-top: auto;
}
';

// Append the CSS fix to the styles.css file
file_put_contents('styles.css', file_get_contents('styles.css') . $cssFixContent, FILE_APPEND);

echo "Club card layout fixed! The cards in the last row should now display correctly.";
echo "<p>Please check the <a href='clubs.html'>clubs page</a> to verify the fix.</p>";
?>