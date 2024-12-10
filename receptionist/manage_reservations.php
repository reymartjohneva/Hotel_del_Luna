<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch reservations for receptionist to manage
$stmt = $pdo->prepare("SELECT r.id, r.guest_id, r.room_id, r.check_in_date, r.check_out_date, r.status, u.username, ro.room_type 
                       FROM reservations r 
                       JOIN users u ON r.guest_id = u.id 
                       JOIN rooms ro ON r.room_id = ro.id 
                       WHERE r.status = 'pending'");
$stmt->execute();
$reservations = $stmt->fetchAll();

// Handle accepting reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_id'])) {
    $reservation_id = $_POST['accept_id'];
    
    // Update the reservation status to 'confirmed'
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE id = ?");
    $stmt->execute([$reservation_id]);

    // Optionally, you can update the room availability as well
    $stmt = $pdo->prepare("UPDATE rooms SET availability = 0 WHERE id = (SELECT room_id FROM reservations WHERE id = ?)");
    $stmt->execute([$reservation_id]);

    echo "Reservation accepted successfully!";
}

// Handle deleting reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $reservation_id = $_POST['delete_id'];

    // Delete the reservation from the database
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);

    echo "Reservation deleted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservations</title>
    <link rel="stylesheet" href="../styles/dashboard.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="dashboard">
        <h2>Manage Reservations</h2>

        <?php if (count($reservations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Room Type</th>
                        <th>Check-in Date</th>
                        <th>Check-out Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['username']) ?></td>
                            <td><?= htmlspecialchars($reservation['room_type']) ?></td>
                            <td><?= htmlspecialchars($reservation['check_in_date']) ?></td>
                            <td><?= htmlspecialchars($reservation['check_out_date']) ?></td>
                            <td><?= htmlspecialchars($reservation['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <button type="submit" name="accept_id" value="<?= $reservation['id'] ?>">Accept</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <button type="submit" name="delete_id" value="<?= $reservation['id'] ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending reservations to manage.</p>
        <?php endif; ?>
    </div>
</body>
</html>
