<?php
// Script to delete soccer results from the database
require 'config.php';

// Function to delete soccer results from the database
function deleteSoccerResults($id) {
    global $pdo;

    // Prepare SQL statement
    $stmt = $pdo->prepare('DELETE FROM results WHERE id = :id');

    // Bind parameters
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();
}

// Example delete (replace with actual data)
$id = 1; // ID of the match to delete

// Call the function to delete results
deleteSoccerResults($id);

echo "Results deleted successfully!";
?>
