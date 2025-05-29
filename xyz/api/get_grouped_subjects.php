<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Connect to DB
    $pdo = new PDO("mysql:host=localhost;dbname=sibsagar_university;charset=utf8mb4", "root", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to group subjects by dept, course, semester
    $sql = "
        SELECT 
            department_id,
            department_name,
            course,
            semester,
            COUNT(subject_id) AS total_subjects,
            GROUP_CONCAT(subject_code ORDER BY subject_code SEPARATOR ',') AS subject_codes_str
        FROM subject_info
        GROUP BY department_id, department_name, course, semester
        ORDER BY department_name, course, semester
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert subject_codes_str to array
    foreach ($results as &$row) {
        $row['subject_codes'] = explode(',', $row['subject_codes_str']);
        unset($row['subject_codes_str']);
    }

    header('Content-Type: application/json');
    echo json_encode($results);

} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'DB Query Error: ' . $e->getMessage()]);
}
