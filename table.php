<?php

$directory = new RecursiveDirectoryIterator('jsons');
$iterator = new RecursiveIteratorIterator($directory);
$allStudentData = [];

// Loop through each file in the directory (and its subdirectories)
foreach ($iterator as $fileInfo) {
    if ($fileInfo->isFile() && $fileInfo->getExtension() === 'json') {
        $filename = $fileInfo->getPathname();
        $jsonString = file_get_contents($filename);
        $studentData = json_decode($jsonString, true);
        if ($studentData !== null) {
            $allStudentData[] = $studentData;
        } else {
            echo "Error decoding JSON from file: $filename\n";
        }
    }
}

$mergedGroups = [];
$processedDepartments = [];

// Iterate through all departments
foreach ($allStudentData as $index => $dept1) {
    if (in_array($index, $processedDepartments)) {
        continue; // Skip already processed departments
    }

    $matchedDepartments = [$dept1]; // Start with current department
    $processedDepartments[] = $index;

    // Check for other matching departments
    for ($j = $index + 1; $j < count($allStudentData); $j++) {
        $dept2 = $allStudentData[$j];

        // Ensure departments have different courses and same student count
        if (
            $dept1["department"] !== $dept2["department"] &&
            $dept1["semester"] === $dept2["semester"] &&
            $dept1["totalStudent"] === $dept2["totalStudent"]
        ) {
            $matchedDepartments[] = $dept2;
            $processedDepartments[] = $j;
        }
    }

    // If at least two departments match the criteria, merge them in zigzag order
    if (count($matchedDepartments) > 1) {
        $mergedStudents = [];
        $numStudents = $matchedDepartments[0]["totalStudent"];

        for ($i = 0; $i < $numStudents; $i++) {
            foreach ($matchedDepartments as $dept) {
                $mergedStudents[] = $dept["students"][$i];
            }
        }

        // Create new merged department
        $mergedGroups[] = [
            "department" => "Merged",
            "semester" => $dept1["semester"],
            "course" => "Mixed",
            "totalStudent" => count($mergedStudents),
            "students" => $mergedStudents
        ];
    }
}

// Output the merged groups
echo '<pre>';
// print_r($mergedGroups);
echo '</pre>';

?>


<?php

// Read the JSON file
$jsonString = file_get_contents('rooms.json');

// Decode the JSON string to a PHP array
$dataArray = json_decode($jsonString, true);

// Print the array in a readable format
echo '<pre>';
print_r($dataArray);
echo '</pre>';

?>
