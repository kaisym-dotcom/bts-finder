<?php
require 'config.php';
require 'simple_html_dom.php';

// Function to fetch Flashscore URL for a given league
function getFlashscoreUrl($leagueId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT flashscore_url FROM leagues WHERE id = :id');
    $stmt->bindParam(':id', $leagueId);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Function to scrape teams from Flashscore
function scrapeTeams($leagueId) {
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

    // Initialize teams array
    $teams = [];

    // Iterate over teams and extract data
    foreach ($dom->find('div.team') as $teamElement) {
        $teamName = $teamElement->find('div.team__name', 0)->plaintext;

        // Add team data to teams array
        $teams[] = [
            'name' => $teamName,
            'league_id' => $leagueId // Include the league ID
        ];
    }

    // Return parsed teams
    return $teams;
}

// Function to store teams in the database
function storeTeams($teams) {
    global $pdo;
    foreach ($teams as $team) {
        // Prepare SQL statement
        $stmt = $pdo->prepare('INSERT INTO teams (name, league_id) VALUES (:name, :league_id)');
        // Bind parameters
        $stmt->bindParam(':name', $team['name']);
        $stmt->bindParam(':league_id', $team['league_id']);
        // Execute statement
        $stmt->execute();
    }
}

// List of league IDs to scrape
$leagueIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// Iterate over league IDs and scrape teams
foreach ($leagueIds as $leagueId) {
    $teams = scrapeTeams($leagueId);

    // Print the teams (for testing purposes)
    echo '<pre>';
    print_r($teams);
    echo '</pre>';

    // Store the teams in the database
    storeTeams($teams);
}

echo "Teams updated successfully!";
?>
