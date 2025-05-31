<?php
// Direct fix for club card layout issues

// Get the current clubs.html content
$clubsHtml = file_get_contents('clubs.html');

// Fix the container structure - wrap the existing cards in rows
$pattern = '/<div id="clubsContainer">([\s\S]*?)<!-- Footer -->/';
preg_match($pattern, $clubsHtml, $matches);

if (isset($matches[1])) {
    $clubsContent = $matches[1];
    
    // Extract all club cards
    preg_match_all('/<div class="col-lg-6 mb-4"[\s\S]*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', $clubsContent, $clubCards);
    
    if (isset($clubCards[0]) && count($clubCards[0]) > 0) {
        $newClubsContent = '<div id="clubsContainer" class="row">';
        
        // Add each club card back to the container
        foreach ($clubCards[0] as $card) {
            $newClubsContent .= $card;
        }
        
        $newClubsContent .= '</div>';
        
        // Replace the old container with the new one
        $newClubsHtml = preg_replace($pattern, $newClubsContent . "\n\n    <!-- Footer -->", $clubsHtml);
        
        // Save the updated clubs.html file
        file_put_contents('clubs.html', $newClubsHtml);
        
        echo "Club cards structure fixed successfully!";
    } else {
        echo "No club cards found in the HTML.";
    }
} else {
    echo "Could not find the clubs container in the HTML.";
}

// Now let's add some CSS to ensure proper layout
$cssContent = '
/* Fix for club card layout */
#clubsContainer {
    display: flex;
    flex-wrap: wrap;
}

#clubsContainer .col-lg-6 {
    display: flex;
    margin-bottom: 1.5rem;
}

#clubsContainer .card {
    width: 100%;
    margin-bottom: 0;
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

// Append the CSS to the styles.css file
file_put_contents('styles.css', file_get_contents('styles.css') . $cssContent, FILE_APPEND);

// Also update the JavaScript to maintain the structure when loading from API
$clubsJs = file_get_contents('clubs.js');

// Update the container initialization
$newClubsJs = str_replace(
    'clubsContainer.innerHTML = "";',
    'clubsContainer.innerHTML = ""; clubsContainer.className = "row";',
    $clubsJs
);

// Save the updated clubs.js file
file_put_contents('clubs.js', $newClubsJs);

// Also update the inline JavaScript in clubs.html
$newClubsHtml = str_replace(
    'clubsContainer.innerHTML = \'\';',
    'clubsContainer.innerHTML = \'\'; clubsContainer.className = "row";',
    $newClubsHtml
);

// Save the updated clubs.html file again
file_put_contents('clubs.html', $newClubsHtml);

echo "<p>CSS and JavaScript also updated to maintain the layout.</p>";
echo "<p>Please check the <a href='clubs.html'>clubs page</a> to verify the fix.</p>";
?>