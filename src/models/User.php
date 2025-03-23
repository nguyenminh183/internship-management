<?php

class User {
    private $user_id;
    private $username;
    private $password;
    private $role;
    private $is_first_login;

    public function __construct($user_id, $username, $password, $role, $is_first_login = 1) {
        $this->user_id = $user_id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->is_first_login = $is_first_login;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
    }

    public function isFirstLogin() {
        return $this->is_first_login;
    }

    public function setPassword($new_password) {
        $this->password = $new_password;
    }

    public function setFirstLogin($is_first_login) {
        $this->is_first_login = $is_first_login;
    }

    public function authenticate($username, $password) {
        // Logic for authenticating user
    }
}