<?php
include('../db/pdo_connect.php');

if (isset($_GET['department_id'])) {
    $departmentId = intval($_GET['department_id']);

    // Fetch courses related to the department
    $sqlCourses = "SELECT DISTINCT course FROM student WHERE department = ?";
    $stmtCourses = $pdo->prepare($sqlCourses);
    $stmtCourses->execute([$departmentId]);
    $courses = [];
    while ($row = $stmtCourses->fetch(PDO::FETCH_ASSOC)) {
        $courses[] = ["id" => $row['course'], "name" => getCourseName($row['course'])];
    }

    // Fetch semesters related to the department
    $sqlSemesters = "SELECT DISTINCT semester FROM student WHERE department = ?";
    $stmtSemesters = $pdo->prepare($sqlSemesters);
    $stmtSemesters->execute([$departmentId]);
    $semesters = [];
    while ($row = $stmtSemesters->fetch(PDO::FETCH_ASSOC)) {
        $semesters[] = $row['semester'];
    }

    echo json_encode(["courses" => $courses, "semesters" => $semesters]);
}

// Function to get course name from course ID (you may store this in a database)
function getCourseName($courseId) {
    $courseNames = [
        1 => "UG",
        2 => "PG",
        3 => "TDC",
        4 => "FYUG"
    ];
    return $courseNames[$courseId] ?? "Unknown";
}
?>
