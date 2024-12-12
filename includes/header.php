<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include the correct stylesheet based on the structure -->
    <link rel="stylesheet" href="assets/style.css?v=1.0">
    <!-- If you need the second stylesheet, ensure the path is correct, else remove it -->
    <link rel="stylesheet" href="../assets/style.css?v=1.0">  
    <title>Hotel Management System</title>
</head>
<style>/* Style for the header */
header {
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    text-align: center;
}

.header-container h1 {
    margin: 0;
    font-size: 30px;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 20px 0 0;
}

nav ul li {
    display: inline;
    margin-right: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
}

nav ul li a:hover {
    color: #f39c12;
}
</style>
<body>
<header>
    <div class="header-container">
        <h1>Hotel Management System</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <ul>
                    <li><a href="/hotelismmm/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="/project108/rooms.php">Rooms</a></li>
                    <li><a href="/project108/logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php else: ?>
            <nav>
                <ul>
                    <li><a href="/project108/login.php">Login</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</header>

<!-- Add your content here -->

</body>
</html>
