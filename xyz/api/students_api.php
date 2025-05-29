<?php
require_once '../bashmodel.php'; // Adjust path as needed


header('Content-Type: application/json');

$student = new Student();
$allStudents = $student->getAll('student_info');

echo json_encode($allStudents);
?>