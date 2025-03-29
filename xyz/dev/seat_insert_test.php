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

// ✅ Merging departments based on department, semester, and course
$mergedDepartments = [];

// Function to generate unique key
function getDeptKey($dept) {
    return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
}

foreach ($departments as $dept) {
    $key = getDeptKey($dept);
    
    if (!isset($mergedDepartments[$key])) {
        // Initialize merged entry
        $mergedDepartments[$key] = [
            "department"   => $dept["department"],
            "semester"     => $dept["semester"],
            "course"       => $dept["course"],
            "totalStudent" => 0,
            "students"     => []
        ];
    }

    // Merge students
    $mergedDepartments[$key]["students"] = array_merge($mergedDepartments[$key]["students"], $dept["students"]);
    $mergedDepartments[$key]["totalStudent"] = count($mergedDepartments[$key]["students"]);
}

// Convert associative array to indexed array
$mergedDepartments = array_values($mergedDepartments);

// ✅ Insert merged data into the database
$seatAllocationListStore = new CreateSeatAllocation();
$seatAllocationListStore->bulkInsert($mergedDepartments);

?>
