<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all available rooms
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE availability = 1");
$stmt->execute();
$rooms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
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
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
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
            background-color: #f1f1f1;
        }

        .room-details {
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 10px 0;
        }

        .room-details h3 {
            margin: 0 0 10px;
        }

        .room-details p {
            margin: 5px 0;
        }

        .status {
            font-weight: bold;
        }

        .back-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <h2>Available Rooms</h2>

    <?php if (count($rooms) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Price</th>
                    <th>Room Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?= htmlspecialchars($room['room_number']) ?></td>
                        <td>$<?= htmlspecialchars($room['price']) ?> /night</td>
                        <td><?= htmlspecialchars($room['room_type']) ?></td>
                        <td class="status"><?= $room['availability'] == 1 ? 'Available' : 'Booked' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No rooms available.</p>
    <?php endif; ?>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

</body>
</html>
