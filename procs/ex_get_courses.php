<?php
header('Content-Type: application/json');

// Assuming you have connected to the database using PDO
include '../db/pdo_connect.php';

if (isset($_GET['department_id'])) {
    $department_id = (int)$_GET['department_id'];

    // Query to get courses for the selected department
    $stmt = $pdo->prepare('SELECT DISTINCT course  FROM subject_info WHERE department_id = ?');
    $stmt->execute([$department_id]);

    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send the response as JSON
    echo json_encode(['courses' => $courses]);
} else {
    echo json_encode(['courses' => []]);  // Return an empty array if no department_id is provided
}
?>
