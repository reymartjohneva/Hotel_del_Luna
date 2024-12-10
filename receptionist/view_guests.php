<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role = 'guest'");
$stmt->execute();
$guests = $stmt->fetchAll();

// Handle deleting guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $guest_id = $_POST['delete_id'];

    // Delete the guest from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$guest_id]);

    echo "Guest deleted successfully!";
}

// Handle editing guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $guest_id = $_POST['edit_id'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    // Update guest details
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$new_username, $new_email, $guest_id]);

    echo "Guest details updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Guests</title>
    <link rel="stylesheet" href="../styles/dashboard.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="dashboard">
        <h2>Manage Guests</h2>

        <?php if (count($guests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guests as $guest): ?>
                        <tr>
                            <td><?= htmlspecialchars($guest['username']) ?></td>
                            <td><?= htmlspecialchars($guest['email']) ?></td>
                            <td><?= htmlspecialchars($guest['role']) ?></td>
                            <td>
                                <!-- Edit Guest Form -->
                                <form method="POST" style="display:inline;">
                                    <input type="text" name="username" value="<?= htmlspecialchars($guest['username']) ?>" required>
                                    <input type="email" name="email" value="<?= htmlspecialchars($guest['email']) ?>" required>
                                    <button type="submit" name="edit_id" value="<?= $guest['id'] ?>">Edit</button>
                                </form>

                                <!-- Delete Guest Form -->
                                <form method="POST" style="display:inline;">
                                    <button type="submit" name="delete_id" value="<?= $guest['id'] ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No guests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
