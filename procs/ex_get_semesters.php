<?php
header('Content-Type: application/json');
require_once '../db/pdo_connect.php'; // Ensure this path is correct

// Get and validate department_id and course_id from query string
$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 0;
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';


if ($department_id <= 0 || empty($course_id)) {
    echo json_encode(['semesters' => []]);  // Return an empty array for invalid input
    exit;
}

try {
    // Prepare and execute the query securely
    $stmt = $pdo->prepare('
        SELECT DISTINCT semester
        FROM subject_info 
        WHERE department_id = ? AND course = ?
        ORDER BY semester ASC
    ');
    $stmt->execute([$department_id, $course_id]);
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['semesters' => $semesters]);
} catch (PDOException $e) {
    // Log error in production, return generic error message in response
    echo json_encode(['semesters' => [], 'error' => 'Database error.']);
}

?>