<?php

class Course {
    private $course_id;
    private $course_code;
    private $course_name;
    private $description;
    private $lecturer_id;
    private $created_at;
    private $updated_at;
    private $conn;
    private $table = 'internship_courses';

    public function __construct($db, $course_code = null, $course_name = null, $description = null, $lecturer_id = null) {
        $this->conn = $db;
        if ($course_code && $course_name && $description && $lecturer_id) {
            $this->course_code = $course_code;
            $this->course_name = $course_name;
            $this->description = $description;
            $this->lecturer_id = $lecturer_id;
            $this->created_at = date("Y-m-d H:i:s");
            $this->updated_at = date("Y-m-d H:i:s");
        }
    }

    public function getCourseId() {
        return $this->course_id;
    }

    public function getCourseCode() {
        return $this->course_code;
    }

    public function getCourseName() {
        return $this->course_name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getLecturerId() {
        return $this->lecturer_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setCourseId($course_id) {
        $this->course_id = $course_id;
    }

    public function setCourseCode($course_code) {
        $this->course_code = $course_code;
    }

    public function setCourseName($course_name) {
        $this->course_name = $course_name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setLecturerId($lecturer_id) {
        $this->lecturer_id = $lecturer_id;
    }

    public function updateTimestamps() {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (course_code, course_name, description, lecturer_id) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sssi", 
            $data['course_code'],
            $data['course_name'],
            $data['description'],
            $data['lecturer_id']
        );

        return $stmt->execute();
    }

    public function getAllByLecturer($lecturer_id) {
        $sql = "SELECT * FROM {$this->table} WHERE lecturer_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $lecturer_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id, $data) {
        // Check if course exists and belongs to lecturer
        $checkSql = "SELECT course_id FROM {$this->table} 
                    WHERE course_id = ? AND lecturer_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $id, $data['lecturer_id']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows === 0) {
            return false;
        }

        // Update course
        $sql = "UPDATE {$this->table} 
                SET course_code = ?, 
                    course_name = ?, 
                    description = ? 
                WHERE course_id = ? AND lecturer_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sssii", 
            $data['course_code'],
            $data['course_name'],
            $data['description'],
            $id,
            $data['lecturer_id']
        );
        
        return $stmt->execute();
    }

    public function delete($id, $lecturer_id) {
        // Check if course exists and belongs to lecturer
        $checkSql = "SELECT course_id FROM {$this->table} 
                     WHERE course_id = ? AND lecturer_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $id, $lecturer_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }

        // Delete the course
        $deleteSql = "DELETE FROM {$this->table} WHERE course_id = ?";
        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $id);
        
        return $deleteStmt->execute();
    }

    public function getById($id, $lecturer_id) {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ? AND lecturer_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $lecturer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

?>