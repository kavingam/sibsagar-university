<?php
require_once 'sleekdbx.php';
// Sample departments (unmerged)
$departments = [
    [
        "department" => 1,
        "semester" => 1,
        "course" => 1,
        "totalStudent" => 2,
        "students" => [
            ["roll_no" => "ASS-UG-SEM01", "name" => "AA-01"],
            ["roll_no" => "ASS-UG-SEM02", "name" => "AA-02"],
        ]
    ],
    [
        "department" => 1,
        "semester" => 2,
        "course" => 1,
        "totalStudent" => 2,
        "students" => [
            ["roll_no" => "ASS-UG-SEM03", "name" => "AA-03"],
            ["roll_no" => "ASS-UG-SEM04", "name" => "AA-04"],
        ]
    ]
];

// ✅ Merge departments by "department, semester, course"
$firstDept = $departments[0];
$secondDept = $departments[1];

// Extract required students from first department
$requiredStudents = array_slice($firstDept["students"], 0, $secondDept["totalStudent"]);

// Initialize merged department array
$mergedDepartments = [];

// Function to generate unique department-semester-course key
function getDeptKey($dept) {
    return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
}

// Store first department
$key1 = getDeptKey($firstDept);
$mergedDepartments[$key1] = [
    "department"   => $firstDept["department"],
    "semester"     => $firstDept["semester"],
    "course"       => $firstDept["course"],
    "totalStudent" => count($firstDept["students"]), // Keep original count
    "students"     => $firstDept["students"]        // Keep original students
];

// Store second department with merged students
$key2 = getDeptKey($secondDept);
$mergedDepartments[$key2] = [
    "department"   => $secondDept["department"],
    "semester"     => $secondDept["semester"],
    "course"       => $secondDept["course"],
    "totalStudent" => count($requiredStudents) + count($secondDept["students"]), // Add students count
    "students"     => array_merge($requiredStudents, $secondDept["students"])   // Merge students properly
];

// Convert associative array to indexed array
$mergedDepartments = array_values($mergedDepartments);

$seatAllocationListStore = new CreateSeatAllocation();

$seatAllocationListStore->bulkInsert($mergedDepartments);
?>