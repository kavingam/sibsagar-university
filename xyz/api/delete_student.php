<?php
require_once '../bashmodel.php'; // Adjust path as needed
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'] ?? '';
    $course = $_POST['course'] ?? '';
    $semester = $_POST['semester'] ?? '';

    if (!$department || !$course || !$semester) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }

    $student = new Student();
    $students = $student->findSimilarStudents($department, $course, $semester);

    $deleted = 0;
    foreach ($students as $stu) {
        if ($student->deleteStudent($stu['roll_no'])) {
            $deleted++;
        }
    }

    echo json_encode([
        'success' => true,
        'deleted_count' => $deleted,
        'message' => "$deleted students deleted from $department - $course - Semester $semester"
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
