<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../bashmodel.php';

if (!class_exists('Student')) {
    die("Error: Student class not found!");
}

$student = new Student();

if (!method_exists($student, 'getAllStudents')) {
    die("Error: getAllStudents() method not found in Student class!");
}

$students = $student->getAllStudents();
if (!$students) {
    die("No students found in database!");
}

echo "<h2>All Students</h2>";
foreach ($students as $s) {
    echo "Roll No: {$s['roll_no']}, Name: {$s['name']}, Department: {$s['department']}<br>";
}
?>