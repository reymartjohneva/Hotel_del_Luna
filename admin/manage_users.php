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

// Handle search functionality
$searchTerm = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%';
}

// Handle role updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];

    if ($user_id === $_SESSION['user_id']) {
        $error = "You cannot modify your own account.";
    } elseif (in_array($new_role, ['receptionist', 'guest'])) {
        $currentRoleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $currentRoleStmt->execute([$user_id]);
        $old_role = $currentRoleStmt->fetchColumn();

        if ($old_role) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
            $stmt->execute([$new_role, $user_id]);

            $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_type, action) VALUES (?, ?, ?)");
            $logStmt->execute([
                $_SESSION['user_id'],
                $_SESSION['role'],
                "Updated user #$user_id role from $old_role to $new_role"
            ]);

            $success = "User role updated successfully.";
        } else {
            $error = "User not found or invalid ID.";
        }
    } else {
        $error = "Invalid role selected.";
    }
}

// Handle user deletions
// Before deleting the user
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE guest_id = ?");
$stmt->execute([$user_id]);
$reservationsCount = $stmt->fetchColumn();

if ($reservationsCount > 0) {
    $error = "Cannot delete this user. There are reservations associated with this user.";
} else {
    // Proceed with deletion
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$user_id]);
    
    $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_type, action) VALUES (?, ?, ?)");
    $logStmt->execute([
        $_SESSION['user_id'],
        $_SESSION['role'],
        "Deleted user #$user_id"
    ]);

    $success = "User deleted successfully.";
}


// Fetch users
if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE username LIKE ? AND role != 'admin'");
    $stmt->execute([$searchTerm]);
    $users = $stmt->fetchAll();
    $totalUsers = count($users); // All matching users
    $totalPages = 1; // Single page for search results
} else {
    $limit = 10; // Users per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE role != 'admin' LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();

    $countStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'");
    $totalUsers = $countStmt->fetchColumn();
    $totalPages = ceil($totalUsers / $limit);
}
?>

<?php include '../includes/header.php'; ?>

<div class="admin-panel">
    <h2>Manage Users</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="GET" action="" style="margin: 20px 0; text-align: center;">
    <input type="text" name="search" placeholder="Search by username" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">Search</button>
</form>


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

    <?php if (!$searchTerm): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>">Page <?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
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

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        margin: 0 5px;
        padding: 5px 10px;
        text-decoration: none;
        color: #007bff;
    }

    .pagination a.active {
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border-radius: 3px;
    }

    /* Styling for the search bar */
form input[type="text"] {
    width: 300px;
    padding: 10px;
    margin-right: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: border-color 0.2s, box-shadow 0.2s;
}

form input[type="text"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

form button {
    padding: 10px 15px;
    font-size: 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}

form button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

</style>
