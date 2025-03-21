<?php
// Script to generate a league table based on the stored results
require 'config.php';

// Function to generate a league table
function generateLeagueTable($league_id, $type = 'total') {
    global $pdo;

    // Base query for generating the league table
    $baseQuery = "
        SELECT 
            team,
            SUM(points) AS points,
            SUM(goal_difference) AS goal_difference,
            COUNT(*) AS played
        FROM (
            SELECT 
                home_team AS team,
                CASE 
                    WHEN home_goals > away_goals THEN 3
                    WHEN home_goals = away_goals THEN 1
                    ELSE 0
                END AS points,
                home_goals - away_goals AS goal_difference
            FROM results
            WHERE league_id = :league_id
            UNION ALL
            SELECT 
                away_team AS team,
                CASE 
                    WHEN away_goals > home_goals THEN 3
                    WHEN away_goals = home_goals THEN 1
                    ELSE 0
                END AS points,
                away_goals - home_goals AS goal_difference
            FROM results
            WHERE league_id = :league_id
        ) AS league_table
        GROUP BY team
        ORDER BY points DESC, goal_difference DESC;
    ";

    // Query for home results
    $homeQuery = "
        SELECT 
            home_team AS team,
            SUM(CASE 
                WHEN home_goals > away_goals THEN 3
                WHEN home_goals = away_goals THEN 1
                ELSE 0
            END) AS points,
            SUM(home_goals - away_goals) AS goal_difference,
            COUNT(*) AS played
        FROM results
        WHERE league_id = :league_id
        GROUP BY home_team
        ORDER BY points DESC, goal_difference DESC;
    ";

    // Query for away results
    $awayQuery = "
        SELECT 
            away_team AS team,
            SUM(CASE 
                WHEN away_goals > home_goals THEN 3
                WHEN away_goals = home_goals THEN 1
                ELSE 0
            END) AS points,
            SUM(away_goals - home_goals) AS goal_difference,
            COUNT(*) AS played
        FROM results
        WHERE league_id = :league_id
        GROUP BY away_team
        ORDER BY points DESC, goal_difference DESC;
    ";

    // Select the appropriate query based on the type parameter
    $query = '';
    switch ($type) {
        case 'home':
            $query = $homeQuery;
            break;
        case 'away':
            $query = $awayQuery;
            break;
        case 'total':
        default:
            $query = $baseQuery;
            break;
    }

    // Prepare and execute the selected query
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':league_id', $league_id);
    $stmt->execute();

    // Fetch all results
    $leagueTable = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the league table
    return $leagueTable;
}

// Function to get match results by league and matchday
function getMatchResults($league_id) {
    global $pdo;

    // Query to get match results
    $query = "
        SELECT 
            home_team, away_team, home_goals, away_goals, matchday
        FROM results
        WHERE league_id = :league_id
        ORDER BY matchday ASC;
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':league_id', $league_id);
    $stmt->execute();

    // Fetch all results
    $matchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the match results
    return $matchResults;
}

// Get the league ID from the URL parameters
$league_id = isset($_GET['league_id']) ? intval($_GET['league_id']) : 0;

// Fetch the league name
$stmt = $pdo->prepare('SELECT name FROM leagues WHERE id = :league_id');
$stmt->bindParam(':league_id', $league_id);
$stmt->execute();
$league = $stmt->fetch(PDO::FETCH_ASSOC);

if ($league) {
    echo "<h1>League Table for {$league['name']}</h1>";

    // Generate and display total league table
    $totalTable = generateLeagueTable($league_id, 'total');
    echo "<h2>Total Table</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Team</th><th>Points</th><th>Goal Difference</th><th>Played</th></tr>";
    foreach ($totalTable as $row) {
        echo "<tr><td>{$row['team']}</td><td>{$row['points']}</td><td>{$row['goal_difference']}</td><td>{$row['played']}</td></tr>";
    }
    echo "</table>";

    // Generate and display home league table
    $homeTable = generateLeagueTable($league_id, 'home');
    echo "<h2>Home Table</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Team</th><th>Points</th><th>Goal Difference</th><th>Played</th></tr>";
    foreach ($homeTable as $row) {
        echo "<tr><td>{$row['team']}</td><td>{$row['points']}</td><td>{$row['goal_difference']}</td><td>{$row['played']}</td></tr>";
    }
    echo "</table>";

    // Generate and display away league table
    $awayTable = generateLeagueTable($league_id, 'away');
    echo "<h2>Away Table</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Team</th><th>Points</th><th>Goal Difference</th><th>Played</th></tr>";
    foreach ($awayTable as $row) {
        echo "<tr><td>{$row['team']}</td><td>{$row['points']}</td><td>{$row['goal_difference']}</td><td>{$row['played']}</td></tr>";
    }
    echo "</table>";

    // Fetch and display match results by matchday
    $matchResults = getMatchResults($league_id);
    echo "<h2>Match Results</h2>";
    $currentMatchday = null;
    foreach ($matchResults as $result) {
        if ($currentMatchday !== $result['matchday']) {
            if ($currentMatchday !== null) {
                echo "</table>";
            }
            $currentMatchday = $result['matchday'];
            echo "<h3>Matchday {$currentMatchday}</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Home Team</th><th>Away Team</th><th>Home Goals</th><th>Away Goals</th></tr>";
        }
        echo "<tr><td>{$result['home_team']}</td><td>{$result['away_team']}</td><td>{$result['home_goals']}</td><td>{$result['away_goals']}</td></tr>";
    }
    if ($currentMatchday !== null) {
        echo "</table>";
    }
} else {
    echo "<h1>League not found</h1>";
}
?>
