<?php
require_once '../src/config/database.php';
require_once '../src/auth/Authentication.php';
require_once '../src/middleware/AuthMiddleware.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);
$middleware = new AuthMiddleware($auth);

// Check if user is logged in and is a lecturer
$middleware->checkRole(['lecturer']);

// Rest of your protected page code here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Internship Management</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <!-- Add your dashboard content here -->
    </div>
</body>
</html>