<?php
/* v.0 debugging
include('../db/pdo_connect.php'); 

header('Content-Type: application/json');

$department = isset($_POST['department']) ? $_POST['department'] : null;
$semester = isset($_POST['semester']) ? $_POST['semester'] : null;
$course = isset($_POST['course']) ? $_POST['course'] : null;

try {
    $sql = "SELECT * FROM student WHERE 1";
    if ($department) {
        $sql .= " AND department = :department";
    }
    if ($semester) {
        $sql .= " AND semester = :semester";
    }
    if ($course) {
        $sql .= " AND course = :course";
    }
    $stmt = $pdo->prepare($sql);
    if ($department) {
        $stmt->bindParam(':department', $department, PDO::PARAM_INT);
    }
    if ($semester) {
        $stmt->bindParam(':semester', $semester, PDO::PARAM_INT);
    }
    if ($course) {
        $stmt->bindParam(':course', $course, PDO::PARAM_INT);
    }
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$students) {
        echo json_encode([]);
    } else {
        echo json_encode($students);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}*/
// Fixed OK
include('../db/pdo_connect.php');

header('Content-Type: application/json');
$department = isset($_POST['department']) ? $_POST['department'] : null;
$semester = isset($_POST['semester']) ? $_POST['semester'] : null;
$course = isset($_POST['course']) ? $_POST['course'] : null;

try {
    $sql = "
        SELECT s.*, d.department_name 
        FROM student s 
        LEFT JOIN departments d ON s.department = d.department_id 
        WHERE 1"; 
    if ($department) {
        $sql .= " AND s.department = :department";
    }
    if ($semester) {
        $sql .= " AND s.semester = :semester";
    }
    if ($course) {
        $sql .= " AND s.course = :course";
    }
    $stmt = $pdo->prepare($sql);
    if ($department) {
        $stmt->bindParam(':department', $department, PDO::PARAM_INT);
    }
    if ($semester) {
        $stmt->bindParam(':semester', $semester, PDO::PARAM_INT);
    }
    if ($course) {
        $stmt->bindParam(':course', $course, PDO::PARAM_INT);
    }
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$students) {
        echo json_encode([]);
    } else {
        echo json_encode($students);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>