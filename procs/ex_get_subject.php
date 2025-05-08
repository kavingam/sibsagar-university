<?php
header('Content-Type: application/json');
require_once '../db/pdo_connect.php';

$department_id = $_GET['department_id'] ?? 0;
$course = $_GET['course_id'] ?? '';
$semester_id = $_GET['semester_id'] ?? 0;

if (!$department_id || !$course || !$semester_id) {
    echo json_encode(['subjects' => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT subject , subject_code FROM subject_info 
                           WHERE department_id = ? AND course = ? AND semester = ?");
    $stmt->execute([$department_id, $course, $semester_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['subjects' => $subjects]);
} catch (PDOException $e) {
    echo json_encode(['subjects' => [], 'error' => 'Database error.']);
}
?>