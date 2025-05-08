<?php
include '../db/pdo_connect.php';
header('Content-Type: application/json');

$departmentId = $_GET['department_id'] ?? null;
if (!$departmentId) {
    echo json_encode(['error' => 'Missing department ID']);
    exit;
}

try {
    // Fetch unique courses
    $stmt = $pdo->prepare("SELECT DISTINCT course FROM subject_info WHERE department_id = ?");
    $stmt->execute([$departmentId]);
    $courseIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $courseMap = [1 => 'UG', 2 => 'PG', 3 => 'TDC', 4 => 'FYUG'];
    $courses = [];
    foreach ($courseIds as $id) {
        $courses[] = [
            'id' => $id,
            'name' => $courseMap[$id] ?? "Course $id"
        ];
    }

    // Fetch semesters
    $stmt = $pdo->prepare("SELECT DISTINCT semester FROM subject_info WHERE department_id = ? ORDER BY semester");
    $stmt->execute([$departmentId]);
    $semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch subjects
    $stmt = $pdo->prepare("SELECT DISTINCT subject_code, subject_title FROM subject_info WHERE department_id = ?");
    $stmt->execute([$departmentId]);
    $subjects = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $subj) {
        $subjects[] = [
            'id' => $subj['subject_code'],
            'name' => $subj['subject_title']
        ];
    }

    echo json_encode([
        'courses' => $courses,
        'semesters' => $semesters,
        'subjects' => $subjects
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
