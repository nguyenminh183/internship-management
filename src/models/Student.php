<?php

class Student {
    private $student_id;
    private $user_id;
    private $student_code;
    private $first_name;
    private $last_name;
    private $phone;
    private $email;
    private $major;
    private $dob;
    private $class_code;

    public function __construct($student_id, $user_id, $student_code, $first_name, $last_name, $phone, $email, $major, $dob, $class_code) {
        $this->student_id = $student_id;
        $this->user_id = $user_id;
        $this->student_code = $student_code;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone = $phone;
        $this->email = $email;
        $this->major = $major;
        $this->dob = $dob;
        $this->class_code = $class_code;
    }

    public function getStudentId() {
        return $this->student_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getStudentCode() {
        return $this->student_code;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getMajor() {
        return $this->major;
    }

    public function getDob() {
        return $this->dob;
    }

    public function getClassCode() {
        return $this->class_code;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setStudentCode($student_code) {
        $this->student_code = $student_code;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setMajor($major) {
        $this->major = $major;
    }

    public function setDob($dob) {
        $this->dob = $dob;
    }

    public function setClassCode($class_code) {
        $this->class_code = $class_code;
    }
}
