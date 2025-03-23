<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

// Check authentication and role
if (!$auth->isAuthenticated() || $auth->getUserRole() !== 'lecturer') {
    header('Location: ../auth/login.php');
    exit;
}

// Get lecturer details
$lecturer_id = $_SESSION['profile_id'];
$query = "SELECT * FROM lecturers WHERE lecturer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$lecturer = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Internship Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Manage Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="students.php">Student Lists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_course.php">Create Course</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_courses.php">Manage Courses</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Welcome, <?php echo htmlspecialchars($lecturer['first_name'] . ' ' . $lecturer['last_name']); ?></h2>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Active Courses</h5>
                        <?php
                        $query = "SELECT COUNT(*) as count FROM internship_courses WHERE lecturer_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $lecturer_id);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        ?>
                        <h2 class="card-text"><?php echo $result['count']; ?></h2>
                        <a href="courses.php" class="btn btn-primary">Manage Courses</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <?php
                        $query = "SELECT COUNT(DISTINCT s.student_id) as count 
                                FROM students s 
                                JOIN student_courses sc ON s.student_id = sc.student_id 
                                JOIN internship_courses ic ON sc.course_id = ic.course_id 
                                WHERE ic.lecturer_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $lecturer_id);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        ?>
                        <h2 class="card-text"><?php echo $result['count']; ?></h2>
                        <a href="students.php" class="btn btn-primary">View Students</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>