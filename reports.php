<?php
// Script to generate reports from the stored data
require 'config.php';

// Function to generate a report of all soccer results
function generateReport() {
    global $pdo;

    // Prepare SQL statement
    $stmt = $pdo->query('SELECT * FROM results');

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results
    return $results;
}

// Call the function to generate a report
$report = generateReport();

// Print the report (for testing purposes)
print_r($report);
?>
