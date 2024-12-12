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
    if (!empty($_GET['table_name'])) {
        $filters[] = "table_name = :table_name";
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
    if (!empty($_GET['table_name'])) $stmt->bindParam(':table_name', $_GET['table_name']);
    
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

        <label for="table_name">Table Name:</label>
        <input type="text" name="table_name" id="table_name" value="<?= htmlspecialchars($_GET['table_name'] ?? '') ?>">

        <button type="submit">Filter</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>User Type</th>
                <th>Action</th>
                <th>Table Name</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['id']) ?></td>
                        <td><?= htmlspecialchars($log['user_id']) ?></td>
                        <td><?= htmlspecialchars($log['user_type']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['table_name']) ?></td>
                        <td><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No logs found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
