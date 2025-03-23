<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';
require_once __DIR__ . '/../../models/Course.php';

// Authentication check
$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

if (!$auth->isAuthenticated() || $auth->getUserRole() !== 'lecturer') {
    header('Location: ../auth/login.php');
    exit;
}

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    $_SESSION['error'] = "Course ID is required";
    header('Location: manage_courses.php');
    exit;
}

$course = new Course($conn);
$courseData = $course->getById($course_id, $_SESSION['profile_id']);

if (!$courseData) {
    $_SESSION['error'] = "Course not found or access denied";
    header('Location: manage_courses.php');
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'course_code' => $_POST['course_code'],
        'course_name' => $_POST['course_name'],
        'description' => $_POST['description'],
        'lecturer_id' => $_SESSION['profile_id']
    ];

    if ($course->update($course_id, $updateData)) {
        $success = 'Course updated successfully!';
        $courseData = $course->getById($course_id, $_SESSION['profile_id']);
    } else {
        $error = 'Failed to update course. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Course</h2>
            <a href="manage_courses.php" class="btn btn-secondary">Back to Courses</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="course_code" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" 
                               value="<?php echo htmlspecialchars($courseData['course_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" 
                               value="<?php echo htmlspecialchars($courseData['course_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                rows="3"><?php echo htmlspecialchars($courseData['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update Course</button>
                        <a href="manage_courses.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>