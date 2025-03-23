<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';

// Authentication check
$db = new Database();
$conn = $db->connect();
$auth = new Authentication($conn);

if (!$auth->isAuthenticated() || $auth->getUserRole() !== 'lecturer') {
    header('Location: ../auth/login.php');
    exit;
}

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    $_SESSION['error'] = "Course ID is required";
    header('Location: manage_courses.php');
    exit;
}

// Get course details
$stmt = $conn->prepare("SELECT * FROM internship_courses WHERE course_id = ? AND lecturer_id = ?");
$stmt->bind_param("ii", $course_id, $_SESSION['profile_id']);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    $_SESSION['error'] = "Course not found or access denied";
    header('Location: manage_courses.php');
    exit;
}

// Get enrolled students
$stmt = $conn->prepare("
    SELECT s.student_id, s.student_code, s.first_name, s.last_name, 
           s.email, s.phone, s.class_code,
           COALESCE(sc.enrolled_date, CURRENT_TIMESTAMP) as enrolled_date
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.student_id
    WHERE sc.course_id = ?
    ORDER BY s.last_name, s.first_name
");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - <?php echo htmlspecialchars($course['course_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Course: <?php echo htmlspecialchars($course['course_name']); ?></h2>
            <div>
                <a href="import_students.php?course_id=<?php echo $course_id; ?>" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Import Students
                </a>
                <a href="manage_courses.php" class="btn btn-secondary">Back to Courses</a>
            </div>
        </div>

        <!-- Course Details Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['course_code']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description'] ?? 'No description available'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Students:</strong> <?php echo $students->num_rows; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($students->num_rows > 0): ?>
            <!-- Students Table -->
            <div class="table-responsive">
                <table id="studentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Class</th>
                            <th>Enrolled Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td><?php echo htmlspecialchars($student['class_code']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($student['enrolled_date'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger remove-student" 
                                            data-student-id="<?php echo $student['student_id']; ?>"
                                            data-student-name="<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <img src="https://via.placeholder.com/150" alt="No students" class="img-fluid" style="max-width: 150px;">
                </div>
                <h3>No Students Enrolled</h3>
                <p class="text-muted">There are no students enrolled in this course yet.</p>
                <a href="import_students.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-excel"></i> Import Students Now
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Remove Student Modal -->
    <div class="modal fade" id="removeStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Remove Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove <span id="studentName"></span> from this course?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($students->num_rows > 0): ?>
                const table = $('#studentsTable').DataTable({
                    "pageLength": 10,
                    "language": {
                        "emptyTable": "No students found"
                    }
                });
            <?php endif; ?>

            const removeModal = new bootstrap.Modal(document.getElementById('removeStudentModal'));
            let studentIdToRemove = null;

            $('.remove-student').click(function() {
                studentIdToRemove = $(this).data('student-id');
                const studentName = $(this).data('student-name');
                $('#studentName').text(studentName);
                removeModal.show();
            });

            $('#confirmRemove').click(function() {
                if (!studentIdToRemove) return;

                $.post('remove_student.php', {
                    student_id: studentIdToRemove,
                    course_id: <?php echo $course_id; ?>
                }, function(response) {
                    if (response.success) {
                        table.row($(`button[data-student-id="${studentIdToRemove}"]`).closest('tr'))
                             .remove()
                             .draw();
                        
                        const alert = `<div class="alert alert-success alert-dismissible fade show">
                            Student removed successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
                        $('.container').prepend(alert);
                    } else {
                        const alert = `<div class="alert alert-danger alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
                        $('.container').prepend(alert);
                    }
                    removeModal.hide();
                });
            });
        });
    </script>
</body>
</html>