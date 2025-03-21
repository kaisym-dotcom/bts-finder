<?php
// Script to update soccer results in the database
require 'config.php';

// Function to update soccer results in the database
function updateSoccerResults($id, $homeGoals, $awayGoals) {
    global $pdo;

    // Prepare SQL statement
    $stmt = $pdo->prepare('UPDATE results SET home_goals = :home_goals, away_goals = :away_goals WHERE id = :id');

    // Bind parameters
    $stmt->bindParam(':home_goals', $homeGoals);
    $stmt->bindParam(':away_goals', $awayGoals);
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();
}

// Example update (replace with actual data)
$id = 1; // ID of the match to update
$homeGoals = 3;
$awayGoals = 2;

// Call the function to update results
updateSoccerResults($id, $homeGoals, $awayGoals);

echo "Results updated successfully!";
?>
