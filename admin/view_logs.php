<?php
// Start the session and include necessary files
session_start();
include '../includes/db.php'; // Database connection
include '../includes/auth.php'; // Authentication and role validation

// Ensure only authorized users can view logs
requireRole('admin');

// Handle GET request for filtering logs
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filters = [];
    if (!empty($_GET['date'])) {
        $filters[] = "DATE(created_at) = :date";
    }
    if (!empty($_GET['user_type'])) {
        $filters[] = "user_type = :user_type";
    }
    if (!empty($_GET['action'])) {
        $filters[] = "action LIKE :action";
    }

    $query = "SELECT * FROM activity_logs";
    if ($filters) {
        $query .= " WHERE " . implode(" AND ", $filters);
    }
    $stmt = $pdo->prepare($query);

    // Bind parameters based on filters
    if (!empty($_GET['date'])) $stmt->bindParam(':date', $_GET['date']);
    if (!empty($_GET['user_type'])) $stmt->bindParam(':user_type', $_GET['user_type']);
    if (!empty($_GET['action'])) $stmt->bindParam(':action', $_GET['action']);
    
    $stmt->execute();
    $logs = $stmt->fetchAll();
}

?>

<!-- Display Logs -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
</head>
<body>
    <h1>Activity Logs</h1>
    <form method="GET">
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">

        <label for="user_type">User Type:</label>
        <input type="text" name="user_type" id="user_type" value="<?= htmlspecialchars($_GET['user_type'] ?? '') ?>">

        <label for="action">Action:</label>
        <input type="text" name="action" id="action" value="<?= htmlspecialchars($_GET['action'] ?? '') ?>">

        <button type="submit">Filter</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Type</th>
                <th>Action</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['id']) ?></td>
                        <td><?= htmlspecialchars($log['user_type']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No logs found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<style>
/* Basic reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body and Page Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fa;
    color: #333;
    line-height: 1.6;
    padding: 20px;
}

/* Header Styling */
h1 {
    text-align: center;
    color: #4CAF50;
    margin-bottom: 20px;
}

/* Form Styling */
form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

form label {
    font-weight: bold;
    margin-right: 10px;
}

form input {
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 200px;
}

form button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

form button:hover {
    background-color: #45a049;
}

/* Table Styling */
table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #4CAF50;
    color: white;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #f1f1f1;
}

/* No Data Found Message */
table td[colspan="4"] {
    text-align: center;
    font-style: italic;
    color: #888;
}
</style>
