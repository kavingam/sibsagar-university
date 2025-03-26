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
    $stdObj = new SeatAllocation();
    $totalStudent =  $stdObj->getTotalStudents($tableData);
    // echo $totalStudent;

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
    // print_r($result);



    switch ($benchSeat) {
        case 1:
              echo "student seat capacity one";
            break;
        case 2:
              echo "student seat capacity two";
            break;
        default:
             echo "default seat capacity";
            break;
    }


    // foreach ($result['results'][0]['mergedGroupe'] as $group) {
    //     echo "Department: {$group['department']}, Course: {$group['course']}, Total Students: {$group['totalStudent']}\n";
    //     foreach ($group['students'] as $student) {
    //         echo " - Roll No: {$student['roll_no']}, Name: {$student['name']}\n";
    //     }
    //     echo "\n";
    // }
    
    // foreach ($result['results'] as $res) { 
    //     echo "Grouped: {$res['grouped']}, Extract: {$res['extract']}, Remainder: {$res['remainder']}\n";
    // }
    

    // foreach ($result['results'] as $res) { // Loop through 'results' array
    //     foreach ($res['mergedGroupe'] as $re) { // Loop through 'mergedGroupe' inside each result
    //         echo $re;
    //         foreach ($re['students'] as $student) { // Loop through 'students'
    //             echo " - Roll No: {$student['roll_no']}, Name: {$student['name']}\n";
    //         }
    //     }
    // }
    


    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();
    // print_r($rooms);

    $targetCapacity = $totalStudent;
    $seatsPerBench = $benchSeat;

    echo "<br>Total Examination Students: " . $totalStudent;
    echo "<br>Seats Per Bench: " . $seatsPerBench;

    $bestRoom = findBestRoom($rooms, $targetCapacity / $seatsPerBench );
    print_r($bestRoom);

    // print_r($bestRoom);
    // Example Usage:
    // $targetCapacity = 50; // User wants a room with ~35 seats
    // $nearestRoom = findNearestRoom($rooms, $targetCapacity);

    // Print Result
    // if ($nearestRoom) {
    //     echo "Nearest Room Found:\n";
    //     echo "Room No: {$nearestRoom['room_no']}\n";
    //     echo "Room Name: {$nearestRoom['room_name']}\n";
    //     echo "Bench Order: {$nearestRoom['bench_order']}\n";
    //     echo "Seat Capacity: {$nearestRoom['seat_capacity']}\n";
    // } else {
    //     echo "No suitable room found.\n";
    // }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Room Allocation</h2>

    <?php if (!empty($bestRoom['room'])): ?>
        <div class="alert alert-success text-center"><?= $bestRoom['adjustment']; ?></div>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Room No</th>
                    <th>Room Name</th>
                    <th>Bench Columns</th>
                    <th>Seat Capacity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bestRoom['room'] as $room): ?>
                    <tr>
                        <td><?= $room['room_no']; ?></td>
                        <td><?= $room['room_name']; ?></td>
                        <td><?= $room['bench_order']; ?></td>
                        <td><?= $room['seat_capacity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4 class="mt-4">Seating Arrangement</h4>

        <?php
            foreach ($bestRoom['room'] as $room) {
                echo "<h5 class='mt-3'>{$room['room_name']} - Seating Layout</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered text-center'>";
                echo "<tbody>";

                $columns = $room['bench_order']; // Number of benches (columns)
                $rows = ceil($room['seat_capacity'] / ($columns * 1 )); // Adjusted rows
                $seatNumberX = 1;
                $seatNumberY = 1;

                for ($r = 0; $r < $rows; $r++) {
                    echo "<tr>";
                    for ($c = 0; $c < $columns; $c++) {
                        if ($seatNumber <= $room['seat_capacity']) {
                            echo "<td>L Seat " . $seatNumberX . " & R Seat " . ($seatNumberY) . "</td>";
                            $seatNumberX ++;
                            $seatNumberY ++;
                        } else {
                            echo "<td></td>"; // Empty cell if seats exceed capacity
                        }
                    }
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            }
            ?>


    <?php else: ?>
        <div class="alert alert-danger text-center">No suitable room found.</div>
    <?php endif; ?>
</div>


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


<?php 
// Function to find the nearest room based on seat capacity
function findNearestRoom($rooms, $targetCapacity) {
    $nearestRoom = null;
    $minDifference = PHP_INT_MAX; // Set initial high difference

    foreach ($rooms as $room) {
        $difference = abs($room['seat_capacity'] - $targetCapacity); // Compute absolute difference

        if ($difference < $minDifference) {
            $minDifference = $difference;
            $nearestRoom = $room;
        }
    }

    return $nearestRoom;
}

?>

<?php 
// Function to find the nearest or adjusted room
// function findBestRoom($rooms, $targetCapacity) {
//     $nearestRoom = null;
//     $alternativeRooms = [];
//     $minDifference = PHP_INT_MAX; // Start with a high difference

//     foreach ($rooms as $room) {
//         $difference = abs($room['seat_capacity'] - $targetCapacity);

//         // Exact match
//         if ($room['seat_capacity'] == $targetCapacity) {
//             return ['room' => $room, 'adjustment' => 'Exact Fit'];
//         }

//         // Best nearest match
//         if ($difference < $minDifference) {
//             $minDifference = $difference;
//             $nearestRoom = $room;
//         }

//         // Store rooms smaller than required (for possible merging)
//         if ($room['seat_capacity'] < $targetCapacity) {
//             $alternativeRooms[] = $room;
//         }
//     }

//     // If no single room fits, try merging smaller rooms
//     $mergedRooms = tryMergeRooms($alternativeRooms, $targetCapacity);
//     if ($mergedRooms) {
//         return ['room' => $mergedRooms, 'adjustment' => 'Merged Multiple Rooms'];
//     }

//     return ['room' => $nearestRoom, 'adjustment' => 'Nearest Available Room'];
// }

function findBestRoom($rooms, $targetCapacity) {
    // Sort rooms by seat capacity (ascending)
    usort($rooms, function($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    $selectedRooms = [];
    $totalSeats = 0;

    foreach ($rooms as $room) {
        $selectedRooms[] = $room;
        $totalSeats += $room['seat_capacity'];

        if ($totalSeats >= $targetCapacity) {
            return [
                'room' => $selectedRooms,
                'adjustment' => (count($selectedRooms) == 1) ? "Single Room Assigned" : "Merged Multiple Rooms"
            ];
        }
    }

    // If no room is suitable, return an error message
    return [
        'room' => [],
        'adjustment' => "No Suitable Room Found (Minimum seat capacity is " . $rooms[0]['seat_capacity'] . ")"
    ];
}
function findBestRoomXY($rooms, $targetCapacity, $seatsPerBench = 2) {
    $selectedRooms = [];
    $totalSeats = 0;

    foreach ($rooms as $room) {
        $benchColumns = $room['bench_order']; // Number of benches
        $maxSeats = $benchColumns * $seatsPerBench; // Each bench holds N seats

        if ($room['seat_capacity'] >= $targetCapacity) {
            // If a single room can fit all students, choose it directly
            return [
                'room' => [$room],
                'adjustment' => "Single Room Assigned with $seatsPerBench seats per bench"
            ];
        }

        // Add room to selected list if capacity isn't enough
        $selectedRooms[] = $room;
        $totalSeats += $room['seat_capacity'];

        // Stop if enough rooms are merged to fit students
        if ($totalSeats >= $targetCapacity) {
            return [
                'room' => $selectedRooms,
                'adjustment' => "Merged Multiple Rooms with $seatsPerBench seats per bench"
            ];
        }
    }

    // If no rooms can fit the students, return an error
    return [
        'room' => [],
        'adjustment' => "No Suitable Room Found"
    ];
}
// Function to try merging smaller rooms to fit capacity
function tryMergeRooms($rooms, $targetCapacity) {
    $merged = [];
    $totalSeats = 0;

    foreach ($rooms as $room) {
        $merged[] = $room;
        $totalSeats += $room['seat_capacity'];

        if ($totalSeats >= $targetCapacity) {
            return $merged;
        }
    }

    return null; // No suitable merge found
}
?>