<?php
// require_once('../db/pdo_connect.php');

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $department = $_POST['department'] ?? '';
//     $semester = $_POST['semester'] ?? '';
//     $course = $_POST['course'] ?? '';

//     $sql = "SELECT subject, subject_code, department_name, semester, course 
//             FROM subject_info 
//             WHERE department_id = ? AND semester = ? AND course = ?";

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$department, $semester, $course]);
//     $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     echo json_encode($subjects);
// }
?>
<?php
header('Content-Type: application/json');
require_once '../db/pdo_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([]);
    exit;
}

$department = $_POST['department'] ?? null;
$semester = $_POST['semester'] ?? null;
$course = $_POST['course'] ?? null;

if (!is_numeric($department) || !is_numeric($semester) || !is_numeric($course)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT subject, subject_code, department_name, semester, course 
                           FROM subject_info 
                           WHERE department_id = ? AND semester = ? AND course = ?");
    $stmt->execute([$department, $semester, $course]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($subjects);
} catch (PDOException $e) {
    echo json_encode([]);
}
