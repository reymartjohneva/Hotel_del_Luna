<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $guest_id = $_GET['id'];

    // Fetch guest data
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ? AND role = 'guest'");
    $stmt->execute([$guest_id]);
    $guest = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update guest information
        $username = $_POST['username'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $guest_id]);

        header('Location: view_guests.php');
        exit;
    }
} else {
    header('Location: view_guests.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guest</title>
</head>
<body>
    <h2>Edit Guest</h2>
    
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($guest['username']) ?>" required>
        
        <br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($guest['email']) ?>" required>
        
        <br><br>

        <button type="submit">Update Guest</button>
    </form>
</body>
</html>
