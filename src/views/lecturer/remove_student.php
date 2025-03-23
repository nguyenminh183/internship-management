<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

if (!$auth->isAuthenticated() || $auth->getUserRole() !== 'lecturer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_POST['course_id'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    
    // Verify the course belongs to the lecturer
    $stmt = $conn->prepare("SELECT course_id FROM internship_courses WHERE course_id = ? AND lecturer_id = ?");
    $stmt->bind_param("ii", $course_id, $_SESSION['profile_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
    
    // Remove student from course
    $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $student_id, $course_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove student']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);