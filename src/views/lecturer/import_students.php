<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/Authentication.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $inputFileName = $_FILES['excel_file']['tmp_name'];
        $spreadsheet = IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Remove header row
        array_shift($rows);
        
        $conn->begin_transaction();
        
        foreach ($rows as $row) {
            if (empty($row[0])) continue; // Skip empty rows
            
            // Insert user if not exists
            $stmt = $conn->prepare("INSERT IGNORE INTO users (username, password, role) VALUES (?, ?, 'student')");
            $username = $row[0]; // student code as username
            $password = password_hash($row[0], PASSWORD_DEFAULT); // student code as initial password
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            
            $user_id = $stmt->insert_id ?: $conn->query("SELECT user_id FROM users WHERE username = '$username'")->fetch_object()->user_id;
            
            // Insert student if not exists
            $stmt = $conn->prepare("INSERT IGNORE INTO students 
                (user_id, student_code, first_name, last_name, email, phone, class_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", 
                $user_id,
                $row[0], // student_code
                $row[1], // first_name
                $row[2], // last_name
                $row[3], // email
                $row[4], // phone
                $row[5]  // class_code
            );
            $stmt->execute();
            
            // Get student_id
            $student_id = $stmt->insert_id ?: $conn->query("SELECT student_id FROM students WHERE student_code = '$row[0]'")->fetch_object()->student_id;
            
            // Enroll in course
            $stmt = $conn->prepare("INSERT IGNORE INTO student_courses (student_id, course_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $course_id);
            $stmt->execute();
        }
        
        $conn->commit();
        $message = "Students imported successfully!";
        $messageType = "success";
        
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Import Students</h2>
            <a href="view_students.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary">Back</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" 
                               accept=".xlsx,.xls" required>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">
                            Excel file should have these columns in order:<br>
                            Student Code, First Name, Last Name, Email, Phone, Class Code
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="download_template.php?course_id=<?php echo $course_id; ?>" 
               class="btn btn-outline-primary">
                Download Template
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>