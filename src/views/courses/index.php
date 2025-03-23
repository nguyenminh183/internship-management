<?php
require_once '../../config/database.php';

class CourseController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getCourses() {
        $query = "SELECT * FROM internship_courses";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$courseController = new CourseController();
$courses = $courseController->getCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
</head>
<body>
    <h1>Available Courses</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Lecturer ID</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['description']); ?></td>
                    <td><?php echo htmlspecialchars($course['lecturer_id']); ?></td>
                    <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($course['updated_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>