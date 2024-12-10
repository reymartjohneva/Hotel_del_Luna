<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all guests
$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role = 'guest'");
$stmt->execute();
$guests = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Delete guest
        $guest_id = $_POST['guest_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$guest_id]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Guests</title>
    <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>
    <h2>Manage Guests</h2>
    
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guests as $guest): ?>
                <tr>
                    <td><?= htmlspecialchars($guest['username']) ?></td>
                    <td><?= htmlspecialchars($guest['email']) ?></td>
                    <td>
                        <a href="edit_guest.php?id=<?= $guest['id'] ?>">Edit</a> | 
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="guest_id" value="<?= $guest['id'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this guest?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</body>
</html>
