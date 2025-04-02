<?php

$departments = [
    [
        "department" => 1,
        "semester" => 1,
        "course" => 1,
        "totalStudent" => 4,
        "student" => [
            ["roll_no"=>"ASS-UG-01", "name"=>"AB"],
            ["roll_no"=>"ASS-UG-02", "name"=>"AC"],
            ["roll_no"=>"ASS-UG-03", "name"=>"AD"],
            ["roll_no"=>"ASS-UG-04", "name"=>"AE"]
        ]
    ],
    [
        "department" => 2,
        "semester" => 1,
        "course" => 1,
        "totalStudent" => 2,
        "student" => [
            ["roll_no"=>"CS-UG-01", "name"=>"AB"],
            ["roll_no"=>"CS-UG-03", "name"=>"AD"]
        ]
    ]
];

// Function to generate the department key
function getDeptKey($dept) {
    return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
}

// Function to slice the student data from the first department based on the total students in the second department
function getDeptStudentSlice($firstDept, $secondDept) {
    return array_slice($firstDept["student"], 0, $secondDept["totalStudent"]);
}

// Function to build department information for each student
function buildDeptArray($dept, $studentSlice = null) {
    // For each student, include department, semester, and course information
    $students = array_map(function($student) use ($dept) {
        return [
            "roll_no" => $student["roll_no"],
            "name" => $student["name"],
            "department" => $dept["department"],
            "semester" => $dept["semester"],
            "course" => $dept["course"]
        ];
    }, $studentSlice ?? $dept["student"]);

    return [
        "department" => $dept["department"],
        "semester" => $dept["semester"],
        "course" => $dept["course"],
        "totalStudent" => $dept["totalStudent"],
        "students" => $students
    ];
}

// Create the final array with department keys and data
function buildFinalArray($departments) {
    $finalArray = [];
    
    $firstDept = $departments[0];
    $secondDept = $departments[1];
    
    // Get the student slice for the first department
    $varBiggestDeptSlice = getDeptStudentSlice($firstDept, $secondDept);
    
    // Build the final array for the first department
    $finalArray[] = buildDeptArray($firstDept, $varBiggestDeptSlice);
    
    // Build the final array for the second department
    $finalArray[] = buildDeptArray($secondDept);
    
    return $finalArray;
}

// Build the final array
$finalArray = buildFinalArray($departments);

// Print the final array
echo '<pre>';
print_r($finalArray);
// print_r($departments);
?>
