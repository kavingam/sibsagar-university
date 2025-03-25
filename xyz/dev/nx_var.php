<?php
require_once '../bashmodel.php';
require_once '../seat_allocation/seat_allocation.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $data = json_decode(file_get_contents('php://input'), true);

//     if (!$data) {
//         echo "<p class='text-danger'>Invalid data received!</p>";
//         exit;
//     }

//     $startTime = htmlspecialchars($data['startTime']);
//     $benchSeat = htmlspecialchars($data['benchSeat']);
//     $tableData = $data['tableData'];

//     echo "<h3>Received Data:</h3>";
//     echo "<p><strong>Start Time:</strong> $startTime</p>";
//     echo "<p><strong>Bench Seat:</strong> $benchSeat</p>";

// usort($tableData, function ($a, $b) {
//     return $b['totalStudent'] <=> $a['totalStudent'];
// });

// echo "<h4>Sorted Table Data (Descending by Total Students):</h4>";
// foreach ($tableData as $index => $row) {
//     echo "Row " . ($index + 1) . ": ";
//     echo "Department: " . $row['department'] . ", ";
//     echo "Course: " . $row['course'] . ", ";
//     echo "Semester: " . $row['semester'] . ", ";
//     echo "Total Students: " . $row['totalStudent'] . "<br>";
// }

//     // Calculate totals
//     $totalStudents = 0;
//     $departments = [];

//     foreach ($tableData as $row) {
//         $department = $row['department'];
//         $totalStudents += $row['totalStudent'];
//         $departments[$department] = true;
//     }

//     $totalDepartments = count($departments);

//     // Display the totals
//     echo "<br><strong>Total Students:</strong> " . $totalStudents . "<br>";
//     echo "<strong>Total Unique Departments:</strong> " . $totalDepartments . "<br>";
// }

?>


<?php

/*
 * $seatAllocation = new SeatAllocation();
 *
 *     switch ($benchSeat) {
 *         case 1:
 *             echo 'single seat bench';
 *             break;
 *         case 2:
 *             echo 'double seat bench';
 *             break;
 *         case 3:
 *             echo 'thriple seat bench';
 *             break;
 *         default:
 *             break;
 *     }
 *
 * }
 */
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $startTime = htmlspecialchars($data['startTime']);
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $tableData = $data['tableData'];

    usort($tableData, function ($a, $b) {
        return $b['totalStudent'] <=> $a['totalStudent'];
    });

    // echo "<pre>";
    // print_r($tableData);

    $students = new Student();
    $x = $students->findSimilarStudents(1, 1, 1);
    echo "Similar Students: \n";
    print_r($x);

    $result = processArray($tableData);

    echo '<h4>Step-by-Step Subtraction Process:</h4>';
    if (!empty($result['steps'])) {
        echo '<pre>' . print_r($result['steps'], true) . '</pre>';
    } else {
        echo '<p>No valid subtraction steps performed.</p>';
    }
    
    echo '<h4>Subtracted Index & Remainder:</h4>';
    if (!empty($result['results'])) {
        echo '<pre>' . print_r($result['results'], true) . '</pre>';
    }
}
?>

<?php
// Quick Sort Function (Descending Order) for totalStudent
function quickSortDesc($arr)
{
    if (count($arr) < 2) {
        return $arr;
    }
    $pivot = $arr[0];
    $left = array_filter(array_slice($arr, 1), fn($x) => $x['totalStudent'] > $pivot['totalStudent']);
    $right = array_filter(array_slice($arr, 1), fn($x) => $x['totalStudent'] <= $pivot['totalStudent']);
    return array_merge(quickSortDesc($left), [$pivot], quickSortDesc($right));
}

// Subtraction Algorithm with Re-Sorting
function processArray($arr)
{
    $steps = [];
    $results = [];  // Store results with subtracted index & remainder

    // Initial sorting by totalStudent in descending order
    $arr = quickSortDesc($arr);

    while (count($arr) > 1) {
        // Perform subtraction: First index - Second index
        $firstValue = $arr[0]['totalStudent'];
        $secondValue = $arr[1]['totalStudent'];
        $newValue = $firstValue - $secondValue;

        // Store the step
        $steps[] = "{$firstValue} - {$secondValue} = $newValue";

        // Store second value and remainder
        $results[] = [
            'subtracted' => $arr[1],
            'remainder' => $newValue
        ];

        // Remove first element
        array_shift($arr);

        // If remainder is greater than zero, replace second element
        if ($newValue > 0) {
            $arr[0]['totalStudent'] = $newValue;  // Update first element with remainder
        } else {
            // If remainder is 0, remove second element as well
            array_shift($arr);
        }

        // Resort array in descending order
        $arr = quickSortDesc($arr);
    }

    return ['steps' => $steps, 'results' => $results];
}
?>