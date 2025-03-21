<?php
require_once 'Database.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function deleteAll($table) {
        $sql = "DELETE FROM $table";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
}

class Student extends BaseModel {
    public function deleteStudent($roll_no) {
        $sql = "DELETE FROM student WHERE roll_no = :roll_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':roll_no' => $roll_no]);
    }
}

// Usage
$student = new Student();
$student->deleteStudent('S101');  // Deletes a specific student
$student->deleteAll('student');   // Deletes all students
?>
