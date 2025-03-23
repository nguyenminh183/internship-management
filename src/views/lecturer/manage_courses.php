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

$course = new Course($conn);
$courses = $course->getAllByLecturer($_SESSION['profile_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Courses</h2>
            <a href="create_course.php" class="btn btn-primary">Create New Course</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <table id="coursesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="edit_course.php?id=<?php echo $row['course_id']; ?>" 
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="view_students.php?course_id=<?php echo $row['course_id']; ?>" 
                               class="btn btn-sm btn-info">View Students</a>
                            <button class="btn btn-sm btn-danger delete-course" 
                                    data-id="<?php echo $row['course_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this course?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let courseIdToDelete = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            // Initialize DataTable
            const table = $('#coursesTable').DataTable();
            
            // Handle delete button click
            $('.delete-course').click(function(e) {
                e.preventDefault();
                courseIdToDelete = $(this).data('id');
                deleteModal.show();
            });
            
            // Handle confirm delete
            $('#confirmDelete').click(function() {
                if (!courseIdToDelete) return;
                
                $.ajax({
                    url: 'delete_course.php',
                    type: 'POST',
                    data: {
                        course_id: courseIdToDelete
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove row from DataTable
                            table.row($(`button[data-id="${courseIdToDelete}"]`).closest('tr'))
                                 .remove()
                                 .draw();
                            
                            // Show success message
                            const alert = `<div class="alert alert-success alert-dismissible fade show">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>`;
                            $('.container').prepend(alert);
                        } else {
                            // Show error message
                            const alert = `<div class="alert alert-danger alert-dismissible fade show">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>`;
                            $('.container').prepend(alert);
                        }
                        deleteModal.hide();
                    },
                    error: function() {
                        const alert = `<div class="alert alert-danger alert-dismissible fade show">
                            An error occurred while deleting the course.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
                        $('.container').prepend(alert);
                        deleteModal.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>