<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all reservations for the logged-in guest
$stmt = $pdo->prepare("SELECT r.room_number, r.room_type, r.price, res.check_in_date, res.check_out_date, res.status
                       FROM reservations res
                       JOIN rooms r ON res.room_id = r.id
                       WHERE res.guest_id = ?");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reservations</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .no-reservations {
            color: #f44336;
            font-size: 18px;
            font-weight: bold;
        }

        .back-btn {
            display: block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Your Reservations</h2>

    <?php if (count($reservations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Price</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['room_number']) ?></td>
                        <td><?= htmlspecialchars($reservation['room_type']) ?></td>
                        <td>$<?= htmlspecialchars($reservation['price']) ?> /night</td>
                        <td><?= htmlspecialchars($reservation['check_in_date']) ?></td>
                        <td><?= htmlspecialchars($reservation['check_out_date']) ?></td>
                        <td><?= htmlspecialchars($reservation['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-reservations">No reservations found.</p>
    <?php endif; ?>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

</body>
</html>
