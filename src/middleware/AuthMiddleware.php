<?php
require_once __DIR__ . '/../auth/Authentication.php';

class AuthMiddleware {
    private $auth;

    public function __construct($auth) {
        $this->auth = $auth;
    }

    public function checkAuth() {
        if (!$this->auth->isAuthenticated()) {
            header('Location: /Buoi6/internship-management/login.php');
            exit;
        }
    }

    public function checkRole($allowedRoles) {
        $this->checkAuth();
        
        $userRole = $this->auth->getUserRole();
        if (!in_array($userRole, $allowedRoles)) {
            header('HTTP/1.1 403 Forbidden');
            die('Access Denied');
        }
    }
}