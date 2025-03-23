<?php
require_once 'src/config/database.php';

$db = new Database();
$conn = $db->connect();

if ($conn) {
    echo "<h3 style='color: green'>Kết nối database thành công!</h3>";
    
    // Test truy vấn
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<h4>Danh sách các bảng trong database:</h4>";
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<h3 style='color: red'>Kết nối database thất bại!</h3>";
}