<?php

class Authentication {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password) {
        $sql = "SELECT users.*, 
                COALESCE(lecturers.lecturer_id, students.student_id) as profile_id,
                CASE 
                    WHEN lecturers.lecturer_id IS NOT NULL THEN 'lecturer'
                    WHEN students.student_id IS NOT NULL THEN 'student'
                END as role
                FROM users 
                LEFT JOIN lecturers ON users.user_id = lecturers.user_id 
                LEFT JOIN students ON users.user_id = students.user_id 
                WHERE users.username = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) { // Tạm thời bỏ password_verify để test
                $this->createSession($user);
                return true;
            }
        }
        return false;
    }

    private function createSession($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_id'] = $user['profile_id'];
        $_SESSION['is_first_login'] = $user['is_first_login'];
        $_SESSION['authenticated'] = true;
    }

    public function isAuthenticated() {
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    public function getCurrentUser() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getUserRole() {
        return isset($_SESSION['role']) ? $_SESSION['role'] : null;
    }
}