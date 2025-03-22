<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $startTime = htmlspecialchars($data['startTime']);
    $benchSeat = htmlspecialchars($data['benchSeat']);
    
    echo "<h3>Received Data:</h3>";
    echo "<p><strong>Start Time:</strong> $startTime</p>";
    echo "<p><strong>Bench Seat:</strong> $benchSeat</p>";
    
    //echo "<pre>" . print_r($tableData, true) . "</pre>";  // Print table data in readable format
    // echo "<h4>Table Data:</h4>";
    // $tableData = $data['tableData'];
    // foreach ($tableData as $index => $row) {
    //     echo "Row " . ($index + 1) . ": ";
    //     echo "Department: " . $row['department'] . ", ";
    //     echo "Course: " . $row['course'] . ", ";
    //     echo "Semester: " . $row['semester'] . ", ";
    //     echo "Total Students: " . $row['totalStudent'] . "<br>";
    // }

    // You can further process $tableData (e.g., save it to a database, generate reports, etc.)

    // Example: Sending a JSON response back to JavaScript
    //$response = ["message" => "Data processed successfully", "receivedData" => $tableData];
// Assign tableData to a PHP variable
$tableData = $data['tableData'];

$totalStudents = 0;
$departments = []; // Array to store unique departments

// Process each row
foreach ($tableData as $index => $row) {
    $department = $row['department'];
    $totalStudents += $row['totalStudent'];
    $departments[$department] = true;
}
// Calculate total unique departments
$totalDepartments = count($departments);

// Display the totals
echo "<br><strong>Total Students:</strong> " . $totalStudents . "<br>";
echo "<strong>Total Unique Departments:</strong> " . $totalDepartments . "<br>";
}
?>
