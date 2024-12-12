<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Fetch pending reservations
$stmt = $pdo->prepare("
    SELECT r.id, r.guest_id, r.room_id, r.check_in_date, r.check_out_date, r.status, 
           u.username AS guest_name, ro.room_type 
    FROM reservations r 
    JOIN users u ON r.guest_id = u.id 
    JOIN rooms ro ON r.room_id = ro.id 
    WHERE r.status = 'pending'
");
$stmt->execute();
$reservations = $stmt->fetchAll();

// Flash message system
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_id'])) {
        $reservation_id = intval($_POST['accept_id']);
        
        // Confirm reservation
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$reservation_id]);
        
        // Optionally update room availability
        $stmt = $pdo->prepare("
            UPDATE rooms 
            SET availability = 0 
            WHERE id = (SELECT room_id FROM reservations WHERE id = ?)
        ");
        $stmt->execute([$reservation_id]);

        $_SESSION['flash_message'] = "Reservation accepted successfully!";
        header('Location: view_reservations.php');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $reservation_id = intval($_POST['delete_id']);

        // Delete reservation
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$reservation_id]);

        $_SESSION['flash_message'] = "Reservation deleted successfully!";
        header('Location: view_reservations.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservations</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        .flash-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Manage Reservations</h2>

        <?php if ($flash_message): ?>
            <div class="flash-message"><?= htmlspecialchars($flash_message) ?></div>
        <?php endif; ?>

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
                            <td><?= htmlspecialchars($reservation['guest_name']) ?></td>
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
