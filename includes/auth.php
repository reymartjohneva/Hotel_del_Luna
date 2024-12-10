<?php
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isManager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'receptionist';
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'guest';
}

function requireRole($role) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
        header("Location: /hotelismmm/login.php");
        exit;
    }
}
function authenticate($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start the session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Store the role for access control
        
        return true;
    }
    
    return false; // Authentication failed
}

// Function to log user actions
function logAction($pdo, $user_id, $action, $table_name, $affected_column = null, $details = null) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, table_name, affected_column, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $table_name, $affected_column, $details]);
}


?>
