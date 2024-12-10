<?php
session_start();
include 'includes/db.php';  // Ensure the path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);  // Role can be 'admin', 'receptionist', or 'guest'

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $role])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 20px 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin-top: 10px;
        }

        .login-button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .login-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="receptionist">Receptionist</option>
            <option value="guest">Guest</option>
        </select>
        <button type="submit">Register</button>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?php echo $success; ?></p>
            <a href="login.php" class="login-button">Login</a>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
