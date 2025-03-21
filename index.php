<?php
// Entry point of the application
require 'config.php';

// Fetch all leagues from the database
$stmt = $pdo->query('SELECT * FROM leagues');
$leagues = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Welcome to the Soccer Results Scraper and Reporter!</h1>";
echo "<h2>Leagues:</h2>";
echo "<ul>";
foreach ($leagues as $league) {
    echo "<li><a href='league_table.php?league_id={$league['id']}'>{$league['name']}</a></li>";
}
echo "</ul>";
?>
