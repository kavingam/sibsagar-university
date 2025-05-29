<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sibsagar_university;charset=utf8mb4", "root", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input || !isset($input['department_id'], $input['course'], $input['semester'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $sql = "DELETE FROM subject_info WHERE department_id = :department_id AND course = :course AND semester = :semester";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':department_id' => $input['department_id'],
        ':course' => $input['course'],
        ':semester' => $input['semester']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Subjects deleted.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
