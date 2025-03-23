<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

$auth->logout();

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page with absolute path
header('Location: /Buoi6/internship-management/src/views/auth/login.php');
exit;