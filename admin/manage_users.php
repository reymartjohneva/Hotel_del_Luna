<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user is logged in and has the 'admin' role
requireRole('admin'); // Only admins can access this page

// Set the current user ID for triggers
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $pdo->exec("SET myapp.current_user_id = '$user_id'");
} else {
    die("User session not found. Please log in again.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];

    if (in_array($new_role, ['receptionist', 'guest'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
        $stmt->execute([$new_role, $user_id]);

        // Log the role update
        $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_type, action) VALUES (?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            $new_role,
            "Updated user #$user_id role to $new_role"
        ]);

        $success = "User role updated successfully.";
    } else {
        $error = "Invalid role selected.";
    }
}


// Handle user deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $user_id = (int)$_POST['user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$user_id]);
    $success = "User deleted successfully.";
}

// Fetch all users except admin
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="admin-panel">
    <h2>Manage Users</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="action" value="update_role">
                            <button type="submit" name="new_role" value="receptionist">Set to Receptionist</button>
                            <button type="submit" name="new_role" value="guest">Set to Guest</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="action" value="delete_user">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Inline CSS for styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }

    .admin-panel {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #f1f1f1;
        color: #555;
    }

    table td {
        background-color: #fff;
    }

    form select, form button {
        padding: 8px 12px;
        margin-right: 5px;
        font-size: 16px;
    }

    form button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    form button:hover {
        background-color: #45a049;
    }

    .success {
        color: green;
        text-align: center;
    }

    .error {
        color: red;
        text-align: center;
    }

    .success, .error {
        font-size: 16px;
        margin-top: 10px;
    }

    a button {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
    }

    a button:hover {
        background-color: #e53935;
    }
</style>
