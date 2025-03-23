<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            echo "Password changed successfully.";
        } else {
            echo "Error changing password: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
    <form method="post">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>
        <button type="submit">Change Password</button>
    </form>
</body>
</html>