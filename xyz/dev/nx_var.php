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

    echo "<pre>";
    // print_r($tableData);
    $students = new Student();
    $x = [];
    
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'], 
            $data['semester'], 
            $data['course'], 
            $data['totalStudent'] // Ensure total students are passed as range
        );
    
        // Store results
        $x[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents // Store retrieved students
        ];
    }

    echo "Similar Students: \n";
    $assignSeats = new AssignSeatsStudent($x);
    $result = processArray($x, $assignSeats);
    print_r($result);



    

}
?>



<?php

// Quick Sort Function (Descending Order) for totalStudent
// function quickSortDesc($arr)
// {
//     if (count($arr) < 2) {
//         return $arr;
//     }
//     $pivot = $arr[0];
//     $left = array_filter(array_slice($arr, 1), fn($x) => $x['totalStudent'] > $pivot['totalStudent']);
//     $right = array_filter(array_slice($arr, 1), fn($x) => $x['totalStudent'] <= $pivot['totalStudent']);
//     return array_merge(quickSortDesc($left), [$pivot], quickSortDesc($right));
// }
?>




<?php
function processArray($arr)
{
    $steps = [];
    $results = [];  // Store results with subtracted index & remainder

    // Initial sorting by totalStudent in descending order
    $arr = quickSortDesc($arr);

    while (count($arr) > 1) {
        // Get the first two elements
        $firstValue = $arr[0]['totalStudent'];
        $secondValue = $arr[1]['totalStudent'];
        $remainderValue = $firstValue - $secondValue;
        $groupedValue = $secondValue * 2; // Double the second value

        // Store the step
        $steps[] = "{$firstValue} - {$secondValue} = $remainderValue";


        // Merge grouped part with arr[1] for proper structure
        $mergedGroup = mergeArrays([$arr[0]], [$arr[1]]);

        // Store the result
        $results[] = [
            'mergedGroupe' => $mergedGroup, // Merged grouped data
            'grouped' => $groupedValue,
            'extract' => $secondValue,
            'remainder' => $remainderValue
        ];

        // Remove first element
        array_shift($arr);

        // If remainder is greater than zero, update second element
        if ($remainderValue > 0) {
            $arr[0]['totalStudent'] = $remainderValue;  // Update first element with remainder
        } else {
            // If remainder is 0, remove second element as well
            array_shift($arr);
        }

        // Resort array in descending order after modification
        $arr = quickSortDesc($arr);
    }

    return ['steps' => $steps, 'results' => $results];
}

// Function to merge two arrays
function mergeArrays($array1, $array2)
{
    return array_merge($array1, $array2); // Merge two arrays
}

function splitArray($arr, $range)
{
    if (!is_array($arr)) {
        return ['grouped' => [], 'remainder' => []]; // Return empty if input is invalid
    }

    // Ensure we're working with a proper list of students
    $grouped = array_slice($arr, 0, $range, true);  // Extract first $range students
    $remainder = array_slice($arr, $range, null, true);  // Extract remaining students

    return ['grouped' => $grouped, 'remainder' => $remainder];
}

// Function to sort an array in descending order by 'totalStudent'
function quickSortDesc($arr)
{
    if (count($arr) < 2) {
        return $arr;
    }

    $pivot = $arr[0];
    $left = [];
    $right = [];

    for ($i = 1; $i < count($arr); $i++) {
        if ($arr[$i]['totalStudent'] >= $pivot['totalStudent']) {
            $left[] = $arr[$i];
        } else {
            $right[] = $arr[$i];
        }
    }

    return array_merge(quickSortDesc($left), [$pivot], quickSortDesc($right));
}
?>


