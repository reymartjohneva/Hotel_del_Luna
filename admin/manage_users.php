<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user is logged in and has the 'admin' role
requireRole('admin'); // Only admins can access this page

// Fetch all users except the admin
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll();

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Handle the POST request to update user roles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['role'];

    // Ensure only valid roles can be set
    if (in_array($new_role, ['admin', 'receptionist', 'guest'])) {
        // Fetch the old role for logging
        $roleStmt = $pdo->prepare("SELECT role, username FROM users WHERE id = ? AND role != 'admin'");
        $roleStmt->execute([$user_id]);
        $user = $roleStmt->fetch();

        if ($user) {
            $old_role = $user['role'];
            $username = $user['username'];

            // Update the user's role
            $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
            $updateStmt->execute([$new_role, $user_id]);

            $success = "User role updated successfully.";
        } else {
            $error = "User not found.";
        }
    } else {
        $error = "Invalid role selected.";
    }
}

// Handle the DELETE request to remove a user
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Delete the user
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $deleteStmt->execute([$delete_id]);

    $success = "User deleted successfully.";
    header("Location: manage_users.php"); // Redirect to refresh the page
    exit;
}

// Fetch the updated users list
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Admin Panel Content -->
<div class="admin-panel">
    <h2>Manage Users</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
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
                            <select name="role">
                                <option value="receptionist" <?= $user['role'] === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                                <option value="guest" <?= $user['role'] === 'guest' ? 'selected' : '' ?>>Guest</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                        <a href="?delete_id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">
                            <button type="button">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
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
