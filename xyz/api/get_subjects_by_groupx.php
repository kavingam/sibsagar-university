<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sibsagar_university;charset=utf8mb4", "root", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $department = $_POST['department'] ?? null;
    $course = $_POST['course'] ?? null;
    $semester = $_POST['semester'] ?? null;

    if (!$department || !$course || !$semester) {
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    $sql = "SELECT subject, subject_code FROM subject_info WHERE department_id = :dept AND course = :course AND semester = :sem ORDER BY subject";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':dept' => $department,
        ':course' => $course,
        ':sem' => $semester
    ]);

    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subjects);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error: ' . $e->getMessage()]);
}
