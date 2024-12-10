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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected room and booking details
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $reservation_date = date('Y-m-d'); // Set the current date as the reservation date

    // Insert the reservation into the database
    $stmt = $pdo->prepare("INSERT INTO reservations (guest_id, room_id, reservation_date, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, ?, 'confirmed')");
    $stmt->execute([$user_id, $room_id, $reservation_date, $check_in, $check_out]);

    // Update the room availability to 'booked'
    $stmt = $pdo->prepare("UPDATE rooms SET availability = 0 WHERE id = ?");
    $stmt->execute([$room_id]);

    echo "Booking successful!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Form container */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
            display: block;
        }

        select, input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            form {
                width: 90%;
            }

            label {
                font-size: 14px;
            }

            select, input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <h2>Book a Room</h2>

    <form method="POST">
        <label for="room_id">Select Room:</label>
        <select name="room_id" id="room_id" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= htmlspecialchars($room['id']) ?>">
                    <?= htmlspecialchars($room['room_type']) ?> - $<?= htmlspecialchars($room['price']) ?> /night
                </option>
            <?php endforeach; ?>
        </select>

        <label for="check_in">Check-in Date:</label>
        <input type="date" name="check_in" id="check_in" required>

        <label for="check_out">Check-out Date:</label>
        <input type="date" name="check_out" id="check_out" required>

        <button type="submit">Book Now</button>
    </form>

</body>
</html>
