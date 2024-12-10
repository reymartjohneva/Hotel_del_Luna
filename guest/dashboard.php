<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header('Location: ../login.php');
    exit;
}

// Ensure the username is set in the session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; // Default value if not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Dashboard</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
        <h3>Guest Panel</h3>
        <ul>
            <li><a href="view_my_reservations.php">View My Reservations</a></li>
            <li><a href="booking.php">Book Now</a></li>
            <li><a href="view_room_details.php">View Room Details</a></li>
        </ul>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
