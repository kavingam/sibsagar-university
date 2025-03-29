<?php

function mergeDepartments($firstDump, $secondDump) {
    // Ensure both departments exist
    if (!$firstDump || !$secondDump) {
        return [];
    }

    // Slice students from the first department based on the second department's total students
    $requiredStudents = array_slice($firstDump["students"], 0, $secondDump["totalStudent"]);

    // Remaining students from the first department
    $remainingStudents = array_slice($firstDump["students"], $secondDump["totalStudent"]);

    // Store merged data
    $mergedDepartments = [];

    // First department (remaining students)
    if (!empty($remainingStudents)) {
        $mergedDepartments[] = [
            "department"   => $firstDump["department"],
            "semester"     => $firstDump["semester"],
            "course"       => $firstDump["course"],
            "totalStudent" => count($remainingStudents), 
            "students"     => $remainingStudents
        ];
    }

    // Second department remains unchanged
    $mergedDepartments[] = [
        "department"   => $secondDump["department"],
        "semester"     => $secondDump["semester"],
        "course"       => $secondDump["course"],
        "totalStudent" => count($secondDump["students"]), // Keep its original count
        "students"     => $secondDump["students"] // Keep original students
    ];

    return $mergedDepartments;
}



// Sample Input Data
$firstDump = [
    "department" => 1,
    "semester" => 1,
    "course" => 1,
    "totalStudent" => 6,
    "students" => [
        ["roll_no" => "ASS-UG-SEM01", "name" => "AA-01"],
        ["roll_no" => "ASS-UG-SEM02", "name" => "AA-02"],
        ["roll_no" => "ASS-UG-SEM03", "name" => "AA-03"],
        ["roll_no" => "ASS-UG-SEM04", "name" => "AA-04"],
        ["roll_no" => "ASS-UG-SEM05", "name" => "AA-05"],
        ["roll_no" => "ASS-UG-SEM06", "name" => "AA-06"]
    ]
];

$secondDump = [
    "department" => 1,
    "semester" => 2,
    "course" => 1,
    "totalStudent" => 3,
    "students" => [
        ["roll_no" => "CS-UG-SEM03", "name" => "CS-01"],
        ["roll_no" => "CS-UG-SEM04", "name" => "CS-02"],
        ["roll_no" => "CS-UG-SEM05", "name" => "CS-03"]
    ]
];

// Call the function
$mergedDepartments = mergeDepartments($firstDump, $secondDump);

// Output Result
echo "<pre>";
print_r($mergedDepartments);
echo "</pre>";

?>