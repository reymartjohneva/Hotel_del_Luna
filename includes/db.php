<?php
// Define your database connection details
$host = 'localhost';       // PostgreSQL host
$dbname = 'hotel_del_luna'; // Your database name
$username = 'postgres';    // Your database username
$password = 'reymart';     // Your database password

// Set up a PDO connection
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}
?>
