<?php
header('Content-Type: application/json'); // Set response type to JSON

// Database connection
include('../db/pdo_connect.php');

// Debug: Check if connection is working
if (!$pdo) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Check if table exists
$tableCheck = $pdo->query("SHOW TABLES LIKE 'student'");
if ($tableCheck->rowCount() == 0) {
    echo json_encode(["error" => "Table 'students' does not exist"]);
    exit();
}

// Get data from AJAX request
$department = $_POST['department'] ?? '';
$course = $_POST['course'] ?? '';
$semester = $_POST['semester'] ?? '';

// Validate input
if (empty($department) || empty($course) || empty($semester)) {
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

// Fetch total students from database
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM student WHERE department = ? AND course = ? AND semester = ?");
    $stmt->execute([$department, $course, $semester]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(["total" => $result['total'] ?? 0]); // Return JSON response
} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>
