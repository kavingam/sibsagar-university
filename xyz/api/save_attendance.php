<?php
require_once '../Database.php'; // adjust path
require_once '../bashmodel.php'; // if you're using model class, or remove

header('Content-Type: application/json');

// Get the JSON input data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!$data || !isset($data['roll_no'], $data['student_status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$database = Database::getInstance();
$db = $database->getConnection();

try {
    // Prepare SQL query to update the attendance status
    $stmt = $db->prepare("
        INSERT INTO attendance_sheet 
            (date, time, roll_no, name, department, semester, course, room_no, room_name, bench_order, student_status)
        VALUES 
            (:date, :time, :roll_no, :name, :department, :semester, :course, '', '', 0, :student_status)
        ON DUPLICATE KEY UPDATE 
            student_status = VALUES(student_status)
    ");

    // Execute query for the student
    $stmt->execute([
        ':date' => date('Y-m-d'), // Use current date
        ':time' => date('H:i:s'), // Use current time
        ':roll_no' => $data['roll_no'],
        ':name' => $data['name'],
        ':department' => getDepartmentIdByName($db, $data['department']),
        ':semester' => $data['semester'],
        ':course' => $data['course'],
        ':student_status' => $data['student_status']
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Helper function to get department ID
function getDepartmentIdByName($db, $name) {
    $stmt = $db->prepare("SELECT id FROM departments WHERE name = :name LIMIT 1");
    $stmt->execute([':name' => $name]);
    return $stmt->fetchColumn() ?: 0;
}
