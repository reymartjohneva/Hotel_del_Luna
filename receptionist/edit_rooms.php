<?php
include '../includes/db.php'; // This should include the PDO connection ($pdo)

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the room details for editing
    $sql = "SELECT * FROM rooms WHERE id = :id"; // Use a prepared statement to prevent SQL injection
    $stmt = $pdo->prepare($sql); // Use $pdo here
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the value to the parameter
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch with the correct method

    // If no room found, redirect to the manage rooms page
    if (!$room) {
        header("Location: manage_rooms.php");
        exit();
    }
}

if (isset($_POST['edit_room'])) {
    $id = $_POST['id'];
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];

    // Prepare the SQL query to update the room
    $sql = "UPDATE rooms SET room_number = :room_number, room_type = :room_type, price = :price WHERE id = :id";
    $stmt = $pdo->prepare($sql); // Use $pdo here as well
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':room_number', $room_number, PDO::PARAM_STR);
    $stmt->bindParam(':room_type', $room_type, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to the manage rooms page after successful update
        header("Location: manage_rooms.php"); // Change this to the actual page that manages rooms
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2]; // Display the error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <h1>Edit Room</h1>
    </header>

    <main>
        <section id="edit-room-form">
            <h3>Edit Room</h3>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($room['id']); ?>">
                <input type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                <select name="room_type" required>
                    <option value="Single" <?php echo $room['room_type'] == 'Single' ? 'selected' : ''; ?>>Single</option>
                    <option value="Double" <?php echo $room['room_type'] == 'Double' ? 'selected' : ''; ?>>Double</option>
                    <option value="Luxury" <?php echo $room['room_type'] == 'Luxury' ? 'selected' : ''; ?>>Luxury</option>
                </select>
                <input type="number" name="price" value="<?php echo htmlspecialchars($room['price']); ?>" required>
                <button type="submit" name="edit_room">Update Room</button>
            </form>
        </section>
    </main>

</body>
</html>
<style>
    /* General body styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Header styles */
header {
    background-color: #4CAF50;
    color: white;
    padding: 20px;
    text-align: center;
}

header h1 {
    margin: 0;
}

/* Main content */
main {
    padding: 20px;
    display: flex;
    justify-content: center;
}

/* Form section */
#edit-room-form {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
}

#edit-room-form h3 {
    margin-bottom: 20px;
    font-size: 1.5em;
    color: #333;
}

/* Input fields */
input[type="text"], input[type="number"], select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 1em;
}

/* Button styles */
button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1em;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

/* Media Query for small screens */
@media (max-width: 600px) {
    #edit-room-form {
        padding: 15px;
    }

    input[type="text"], input[type="number"], select {
        font-size: 0.9em;
    }

    button[type="submit"] {
        font-size: 0.9em;
    }
}

</style>
