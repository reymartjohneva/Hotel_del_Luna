<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in and has the correct role (admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ensure the username is set in the session, if not set a default value
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="view_guests.php">View Guests</a></li>
            <li><a href="edit_guests.php">Edit Guests</a></li>
            <li><a href="manage_reservations.php">View Reservations</a></li>
            <li><a href="view_logs.php">View Logs</a></li>
        </ul>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
