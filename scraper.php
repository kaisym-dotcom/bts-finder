<?php
require 'config.php';
require 'simple_html_dom.php';
require 'store.php'; // Include the store functionality

// Function to fetch Flashscore URL for a given league
function getFlashscoreUrl($leagueId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT flashscore_url FROM leagues WHERE id = :id');
    $stmt->bindParam(':id', $leagueId);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Function to scrape soccer results from Flashscore
function scrapeSoccerResults($leagueId) {
    // Fetch the Flashscore URL for the league
    $url = getFlashscoreUrl($leagueId);

    if (!$url) {
        echo "Flashscore URL not found for league ID: $leagueId\n";
        return [];
    }

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

    // Extract league information (for example, from the page title or a specific element)
    $leagueName = $dom->find('title', 0)->plaintext; // Adjust the selector based on the actual HTML structure

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
            'matchday' => $matchday,
            'league_id' => $leagueId // Include the league ID
        ];
    }

    // Return parsed results
    return $results;
}

// List of league IDs to scrape
$leagueIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// Iterate over league IDs and scrape results
foreach ($leagueIds as $leagueId) {
    $results = scrapeSoccerResults($leagueId);

    // Print the results
    echo '<pre>';
    print_r($results);
    echo '</pre>';

    // Store the results in the database
    storeSoccerResults($results);
}

echo "Results stored successfully!";
?>
