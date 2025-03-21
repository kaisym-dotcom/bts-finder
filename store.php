<?php
// Script to store scraped results into the database
require 'config.php';

// Function to store soccer results into the database
function storeSoccerResults($results) {
    global $pdo;

    foreach ($results as $result) {
        // Prepare SQL statement
        $stmt = $pdo->prepare('INSERT INTO results (home_team, away_team, home_goals, away_goals, matchday, league_id) VALUES (:home_team, :away_team, :home_goals, :away_goals, :matchday, :league_id)');

        // Bind parameters
        $stmt->bindParam(':home_team', $result['home_team']);
        $stmt->bindParam(':away_team', $result['away_team']);
        $stmt->bindParam(':home_goals', $result['home_goals']);
        $stmt->bindParam(':away_goals', $result['away_goals']);
        $stmt->bindParam(':matchday', $result['matchday']);
        $stmt->bindParam(':league_id', $result['league_id']);

        // Execute statement
        $stmt->execute();
    }
}

// Example results (replace with actual scraped results)
$results = [
    ['home_team' => 'Team A', 'away_team' => 'Team B', 'home_goals' => 2, 'away_goals' => 1, 'matchday' => 1, 'league_id' => 1],
    // ...
];

// Call the function to store results
storeSoccerResults($results);

echo "Results stored successfully!";
?>
