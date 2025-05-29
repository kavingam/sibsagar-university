<?php
// Include the necessary files
require_once '../bashmodel.php';


// Create an instance of the Student class
$studentModel = new Student();

// Get the POST data from the request
$department = $_POST['department'];
$course = $_POST['course'];
$semester = $_POST['semester'];

// Fetch students based on department, course, and semester
$students = $studentModel->findSimilarStudents($department, $course, $semester);

// Return the student data as JSON
echo json_encode($students);
?>


