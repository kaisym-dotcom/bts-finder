<?php
require 'config.php';
require 'simple_html_dom.php';

// Function to scrape soccer results from Flashscore
function scrapeSoccerResults() {
    // URL for Flashscore soccer results
    $url = 'https://www.flashscore.com/soccer/';

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

    // Execute cURL session and get HTML
    $html = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Parse the HTML using simple_html_dom
    $dom = new simple_html_dom();
    $dom->load($html);

    // Initialize results array
    $results = [];

    // Iterate over matches and extract data
    foreach ($dom->find('div.event__match') as $match) {
        $homeTeam = $match->find('div.event__participant--home', 0)->plaintext;
        $awayTeam = $match->find('div.event__participant--away', 0)->plaintext;
        $homeGoals = $match->find('div.event__score--home', 0)->plaintext;
        $awayGoals = $match->find('div.event__score--away', 0)->plaintext;
        $matchday = $match->find('div.event__round', 0)->plaintext;

        // Add match data to results array
        $results[] = [
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'home_goals' => $homeGoals,
            'away_goals' => $awayGoals,
            'matchday' => $matchday
        ];
    }

    // Return parsed results
    return $results;
}

// Call the function to scrape results
$results = scrapeSoccerResults();

// Print the results (for testing purposes)
print_r($results);
?>
