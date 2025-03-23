<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

// Check authentication and role
if (!$auth->isAuthenticated() || $auth->getUserRole() !== 'student') {
    header('Location: ../auth/login.php');
    exit;
}

// Get student details
$student_id = $_SESSION['profile_id'];
$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
                        <a class="nav-link" href="internships.php">My Internships</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
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
                <h2>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                <p>Student ID: <?php echo htmlspecialchars($student['student_code']); ?></p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Current Internships</h5>
                        <?php
                        $query = "SELECT COUNT(*) as count 
                                FROM internship_details 
                                WHERE student_id = ? AND status = 'approved'";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $student_id);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        ?>
                        <h2 class="card-text"><?php echo $result['count']; ?></h2>
                        <a href="internships.php" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Updates</h5>
                        <div class="list-group">
                            <?php
                            $query = "SELECT ic.course_name, id.status, id.updated_at 
                                    FROM internship_details id 
                                    JOIN internship_courses ic ON id.course_id = ic.course_id 
                                    WHERE id.student_id = ? 
                                    ORDER BY id.updated_at DESC LIMIT 5";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $student_id);
                            $stmt->execute();
                            $results = $stmt->get_result();
                            
                            while ($row = $results->fetch_assoc()) {
                                echo '<div class="list-group-item">';
                                echo '<h6 class="mb-1">' . htmlspecialchars($row['course_name']) . '</h6>';
                                echo '<p class="mb-1">Status: ' . ucfirst(htmlspecialchars($row['status'])) . '</p>';
                                echo '<small>Updated: ' . date('d/m/Y H:i', strtotime($row['updated_at'])) . '</small>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>