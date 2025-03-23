<?php
session_start();
require_once '../../config/database.php';
require_once '../../utils/ExcelHelper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel'])) {
    $file = $_FILES['excel']['tmp_name'];
    $spreadsheet = ExcelHelper::loadSpreadsheet($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($sheetData as $row) {
        $student_code = $row['A'];
        $last_name = $row['B'];
        $first_name = $row['C'];
        $phone = $row['D'];
        $email = $row['E'];
        $major = $row['F'];
        $dob = $row['G'];
        $class_code = $row['H'];

        $stmt = $conn->prepare("INSERT INTO students (student_code, last_name, first_name, phone, email, major, dob, class_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $student_code, $last_name, $first_name, $phone, $email, $major, $dob, $class_code);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
    echo "Import thành công!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students</title>
</head>
<body>
    <h1>Import Student Data</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="excel" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>