<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Add debug information
    echo "Attempting login with: <br>";
    echo "Username: " . $username . "<br>";
    echo "Password: " . $password . "<br>";

    if ($auth->login($username, $password)) {
        $role = $auth->getUserRole();
        echo "Login successful! Role: " . $role . "<br>";
        
        // Ensure the role exists before redirecting
        if ($role) {
            if (isset($_SESSION['is_first_login']) && $_SESSION['is_first_login']) {
                header('Location: change_password.php');
            } else {
                if ($role === 'lecturer') {
                    header('Location: ../lecturer/dashboard.php');
                } else {
                    header('Location: ../student/dashboard.php');
                }
            }
            exit;
        } else {
            $error = 'Role not determined';
        }
    } else {
        $error = 'Invalid username or password';
    }
}

// Debug Session
echo "<pre>";
echo "Session data:<br>";
print_r($_SESSION);
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>