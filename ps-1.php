<?php
/*
$mergedStudents = [
    55 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 3],
    56 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 1],
    57 => ["roll_no" => "ASS-PG-SEM-1029", "name" => "AAA-29", "department" => 1, "semester" => 1, "course" => 2],
    58 => ["roll_no" => "ASS-PG-SEM-1030", "name" => "AAA-30", "department" => 1, "semester" => 1, "course" => 2],
    59 => ["roll_no" => "ASS-PG-SEM-1031", "name" => "AAA-31", "department" => 1, "semester" => 1, "course" => 2],
    60 => ["roll_no" => "ASS-PG-SEM-1032", "name" => "AAA-32", "department" => 1, "semester" => 1, "course" => 2],
    61 => ["roll_no" => "ASS-PG-SEM-1033", "name" => "AAA-33", "department" => 1, "semester" => 1, "course" => 2],
    62 => ["roll_no" => "ASS-PG-SEM-1034", "name" => "AAA-34", "department" => 1, "semester" => 1, "course" => 2],
    63 => ["roll_no" => "ASS-PG-SEM-1035", "name" => "AAA-35", "department" => 1, "semester" => 1, "course" => 2],
    64 => ["roll_no" => "ASS-PG-SEM-1036", "name" => "AAA-36", "department" => 1, "semester" => 1, "course" => 2],
    65 => ["roll_no" => "ASS-PG-SEM-1037", "name" => "AAA-37", "department" => 1, "semester" => 1, "course" => 2],
    66 => ["roll_no" => "ASS-PG-SEM-1038", "name" => "AAA-38", "department" => 1, "semester" => 1, "course" => 2],
    67 => ["roll_no" => "ASS-PG-SEM-1039", "name" => "AAA-39", "department" => 1, "semester" => 1, "course" => 2],
    68 => ["roll_no" => "ASS-UG-SEM-1040", "name" => "BBB", "department" => 2, "semester" => 1, "course" => 1],
    69 => ["roll_no" => "ASS-PG-SEM-1041", "name" => "CCC", "department" => 2, "semester" => 1, "course" => 2],
    70 => ["roll_no" => "ASS-PG-SEM-1042", "name" => "DDD", "department" => 2, "semester" => 1, "course" => 2],
];

$uniqueStudents = [];
$duplicateStudents = [];

// Track seen department-semester-course combinations
$seenCombinations = [];

foreach ($mergedStudents as $key => $student) {
    // Unique identifier based on department, semester, and course
    $identifier = $student['department'] . '-' . $student['semester'] . '-' . $student['course'];

    if (!isset($seenCombinations[$identifier])) {
        // If this combination is not seen before, add it to unique students
        $seenCombinations[$identifier] = true;
        $uniqueStudents[$key] = $student;
    } else {
        // If it's repeated, move it to duplicate students
        $duplicateStudents[$key] = $student;
    }
}

// Display Results
echo "<pre>Unique Students:\n";
print_r($uniqueStudents);
echo "</pre>";

echo "<pre>Duplicate Students:\n";
print_r($duplicateStudents);
echo "</pre>";
*/
?>


<?php
/*
$mergedStudents = [
    55 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 3],
    56 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 1],
    57 => ["roll_no" => "ASS-PG-SEM-1029", "name" => "AAA-29", "department" => 1, "semester" => 1, "course" => 2],
    58 => ["roll_no" => "ASS-PG-SEM-1030", "name" => "AAA-30", "department" => 1, "semester" => 1, "course" => 2],
    59 => ["roll_no" => "ASS-PG-SEM-1031", "name" => "AAA-31", "department" => 1, "semester" => 1, "course" => 2],
    60 => ["roll_no" => "ASS-PG-SEM-1032", "name" => "AAA-32", "department" => 1, "semester" => 1, "course" => 2],
    61 => ["roll_no" => "ASS-PG-SEM-1033", "name" => "AAA-33", "department" => 1, "semester" => 1, "course" => 2],
    62 => ["roll_no" => "ASS-PG-SEM-1034", "name" => "AAA-34", "department" => 1, "semester" => 1, "course" => 2],
    63 => ["roll_no" => "ASS-PG-SEM-1035", "name" => "AAA-35", "department" => 1, "semester" => 1, "course" => 2],
    64 => ["roll_no" => "ASS-PG-SEM-1036", "name" => "AAA-36", "department" => 1, "semester" => 1, "course" => 2],
    65 => ["roll_no" => "ASS-PG-SEM-1037", "name" => "AAA-37", "department" => 1, "semester" => 1, "course" => 2],
    66 => ["roll_no" => "ASS-PG-SEM-1038", "name" => "AAA-38", "department" => 1, "semester" => 1, "course" => 2],
    67 => ["roll_no" => "ASS-PG-SEM-1039", "name" => "AAA-39", "department" => 1, "semester" => 1, "course" => 2],
    68 => ["roll_no" => "ASS-UG-SEM-1040", "name" => "BBB", "department" => 2, "semester" => 1, "course" => 1],
    69 => ["roll_no" => "ASS-PG-SEM-1041", "name" => "CCC", "department" => 2, "semester" => 1, "course" => 2],
    70 => ["roll_no" => "ASS-PG-SEM-1042", "name" => "DDD", "department" => 2, "semester" => 1, "course" => 2],
];

$orderedGroups = [];
$tempGroup = [];
$lastIdentifier = null;

foreach ($mergedStudents as $key => $student) {
    // Create identifier for department, semester, and course
    $identifier = $student['department'] . '-' . $student['semester'] . '-' . $student['course'];

    if ($lastIdentifier === null || $lastIdentifier === $identifier) {
        // If it's the same order, keep adding to the current group
        $tempGroup[$key] = $student;
    } else {
        // If a new order appears, store the previous group and start a new one
        if (!empty($tempGroup)) {
            $orderedGroups[] = $tempGroup;
        }
        $tempGroup = [$key => $student];
    }

    $lastIdentifier = $identifier;
}

// Store the last group
if (!empty($tempGroup)) {
    $orderedGroups[] = $tempGroup;
}

// Display Results
echo "<pre>Extracted Ordered Groups:\n";
print_r($orderedGroups);
echo "</pre>";
*/
?>
<?php
$mergedStudents = [
    55 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 3],
    56 => ["roll_no" => "ASS-UG-SEM-1029", "name" => "AAA", "department" => 1, "semester" => 1, "course" => 1],
    57 => ["roll_no" => "ASS-PG-SEM-1029", "name" => "AAA-29", "department" => 1, "semester" => 1, "course" => 2],
    58 => ["roll_no" => "ASS-PG-SEM-1030", "name" => "AAA-30", "department" => 1, "semester" => 1, "course" => 2],
    59 => ["roll_no" => "ASS-PG-SEM-1031", "name" => "AAA-31", "department" => 1, "semester" => 1, "course" => 2],
    60 => ["roll_no" => "ASS-PG-SEM-1032", "name" => "AAA-32", "department" => 1, "semester" => 1, "course" => 2],
    61 => ["roll_no" => "ASS-PG-SEM-1033", "name" => "AAA-33", "department" => 1, "semester" => 1, "course" => 2],
    62 => ["roll_no" => "ASS-PG-SEM-1034", "name" => "AAA-34", "department" => 1, "semester" => 1, "course" => 2],
    63 => ["roll_no" => "ASS-PG-SEM-1035", "name" => "AAA-35", "department" => 1, "semester" => 1, "course" => 2],
    64 => ["roll_no" => "ASS-PG-SEM-1036", "name" => "AAA-36", "department" => 1, "semester" => 1, "course" => 2],
    65 => ["roll_no" => "ASS-PG-SEM-1037", "name" => "AAA-37", "department" => 1, "semester" => 1, "course" => 2],
    66 => ["roll_no" => "ASS-PG-SEM-1038", "name" => "AAA-38", "department" => 1, "semester" => 1, "course" => 2],
    67 => ["roll_no" => "ASS-PG-SEM-1039", "name" => "AAA-39", "department" => 1, "semester" => 1, "course" => 2],
    68 => ["roll_no" => "ASS-UG-SEM-1040", "name" => "BBB", "department" => 2, "semester" => 1, "course" => 1],
    69 => ["roll_no" => "ASS-PG-SEM-1041", "name" => "CCC", "department" => 2, "semester" => 1, "course" => 2],
    70 => ["roll_no" => "ASS-PG-SEM-1042", "name" => "DDD", "department" => 2, "semester" => 1, "course" => 2],
];

$groupedStudents = [];
$remainingStudents = [];

$currentGroup = [];
$lastIdentifier = null;

foreach ($mergedStudents as $key => $student) {
    // Create an identifier based on department, semester, and course
    $identifier = $student['department'] . '-' . $student['semester'] . '-' . $student['course'];

    if ($lastIdentifier === null || $lastIdentifier === $identifier) {
        // Continue adding to the current group
        $currentGroup[$key] = $student;
    } else {
        // When a new department-semester-course is found, cut and store the last group
        if (count($currentGroup) > 1) {
            $groupedStudents[] = $currentGroup; // Store the full group
        } else {
            $remainingStudents += $currentGroup; // Store single entries separately
        }
        $currentGroup = [$key => $student]; // Start a new group
    }

    $lastIdentifier = $identifier;
}

// Store the last group
if (count($currentGroup) > 1) {
    $groupedStudents[] = $currentGroup;
} else {
    $remainingStudents += $currentGroup;
}

// Display the grouped results
echo "<pre>Grouped Students:\n";
print_r($groupedStudents);
echo "</pre>";

echo "<pre>Remaining Students:\n";
print_r($remainingStudents);
echo "</pre>";
?>
