<?php
include '../includes/db.php';  // This should include the PDO connection ($pdo)

// Handle form submissions for adding a new room
if (isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];

    // Use PDO to prepare and execute the SQL query
    $sql = "INSERT INTO rooms (room_number, room_type, price) VALUES ('$room_number', '$room_type', '$price')";
    if ($pdo->query($sql)) {
        echo "New room added successfully.";
    } else {
        echo "Error: " . $pdo->errorInfo()[2]; // Fetch and display the error message
    }
}

// Handle room deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM rooms WHERE id=$id";
    if ($pdo->query($sql)) {
        echo "Room deleted successfully.";
    } else {
        echo "Error: " . $pdo->errorInfo()[2];
    }
}

// Handle room editing (update)
if (isset($_POST['edit_room'])) {
    $id = $_POST['id'];
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];

    $sql = "UPDATE rooms SET room_number='$room_number', room_type='$room_type', price='$price' WHERE id=$id";
    if ($pdo->query($sql)) {
        echo "Room updated successfully.";
    } else {
        echo "Error: " . $pdo->errorInfo()[2];
    }
}

// Fetch rooms from database
$sql = "SELECT * FROM rooms";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <h1>Room Management</h1>
    </header>

    <main>
        <section id="room-management">
            <h2>Manage Rooms</h2>

<!-- Go Back Button -->
<div class="go-back-button">
    <a href="dashboard.php">
        <button type="button">Go Back to Dashboard</button>
    </a>
</div>

            <!-- Add Room Form -->
            <div id="add-room-form">
                <h3>Add New Room</h3>
                <form method="POST" action="">
                    <input type="text" name="room_number" placeholder="Room Number" required>
                    <select name="room_type" required>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Luxury">Luxury</option>
                    </select>
                    <input type="number" name="price" placeholder="Price" required>
                    <button type="submit" name="add_room">Add Room</button>
                </form>
            </div>

            <!-- Rooms Table -->
            <h3>Rooms List</h3>
            <table id="rooms-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?php echo $room['room_number']; ?></td>
                            <td><?php echo $room['room_type']; ?></td>
                            <td><?php echo $room['price']; ?></td>
                            <td>
                                <a href="edit_rooms.php?id=<?php echo $room['id']; ?>">Edit</a>
                                <a href="?delete=<?php echo $room['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
<style>/* General styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
}

header {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

h1 {
    margin: 0;
    font-size: 2em;
}

main {
    padding: 20px;
}

/* Room management section */
#room-management {
    max-width: 1000px;
    margin: 0 auto;
}

h2 {
    font-size: 1.8em;
    color: #333;
    margin-bottom: 20px;
}

/* Add Room Form */
#add-room-form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

#add-room-form h3 {
    font-size: 1.5em;
    margin-bottom: 10px;
}

#add-room-form form {
    display: flex;
    flex-direction: column;
}

#add-room-form input, 
#add-room-form select, 
#add-room-form button {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 1em;
}

#add-room-form input[type="text"], 
#add-room-form input[type="number"] {
    width: 100%;
}

#add-room-form button {
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

#add-room-form button:hover {
    background-color: #45a049;
}

/* Rooms Table */
#rooms-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

#rooms-table th, 
#rooms-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

#rooms-table th {
    background-color: #333;
    color: white;
}

#rooms-table td a {
    color: #007BFF;
    text-decoration: none;
    margin: 0 10px;
}

#rooms-table td a:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    #add-room-form {
        padding: 15px;
    }

    #add-room-form input, 
    #add-room-form select, 
    #add-room-form button {
        width: 100%;
    }

    #rooms-table {
        font-size: 0.9em;
    }

    #rooms-table th, 
    #rooms-table td {
        padding: 8px;
    }
}

footer {
    text-align: center;
    padding: 10px;
    background-color: #333;
    color: white;
    position: absolute;
    bottom: 0;
    width: 100%;
}
/* Go Back Button */
.go-back-button {
    margin-top: 20px;
    text-align: center;
}

.go-back-button button {
    padding: 12px 20px;
    background-color: #f44336; /* Red color */
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1em;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.go-back-button button:hover {
    background-color: #d32f2f; /* Darker red on hover */
}

</style>