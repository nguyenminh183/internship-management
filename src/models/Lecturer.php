<?php

class Lecturer {
    private $lecturer_id;
    private $user_id;
    private $first_name;
    private $last_name;
    private $email;
    private $department;
    private $created_at;
    private $updated_at;

    public function __construct($lecturer_id, $user_id, $first_name, $last_name, $email, $department) {
        $this->lecturer_id = $lecturer_id;
        $this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->department = $department;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function getLecturerId() {
        return $this->lecturer_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getDepartment() {
        return $this->department;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function updateDetails($first_name, $last_name, $email, $department) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->department = $department;
        $this->updated_at = date("Y-m-d H:i:s");
    }
}