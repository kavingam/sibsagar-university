<?php

/*
 * $directory = new RecursiveDirectoryIterator('jsons');
 * $iterator = new RecursiveIteratorIterator($directory);
 * $allStudentData = [];
 *
 *
 * // Loop through each file in the directory (and its subdirectories)
 * foreach ($iterator as $fileInfo) {
 *     if ($fileInfo->isFile() && $fileInfo->getExtension() === 'json') {
 *         $filename = $fileInfo->getPathname();
 *         $jsonString = file_get_contents($filename);
 *         $studentData = json_decode($jsonString, true);
 *         if ($studentData !== null) {
 *             $allStudentData[] = $studentData;
 *         } else {
 *             echo "Error decoding JSON from file: $filename\n";
 *         }
 *     }
 * }
 */

$directory = 'database/seatAllocationList/data/';  // Define the base directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$allStudentData = [];  // Array to store all student data

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

// Step 1: Sort Descending
usort($allStudentData, function ($a, $b) {
    return $b['totalStudent'] <=> $a['totalStudent'];
});

// echo '<pre>';
// print_r($allStudentData);
// echo '<pre/>';

$mergedGroups = [];
$processedDepartments = [];

// Iterate through all departments
foreach ($allStudentData as $index => $dept1) {
    if (in_array($index, $processedDepartments)) {
        continue;  // Skip already processed departments
    }

    $matchedDepartments = [$dept1];  // Start with current department
    $processedDepartments[] = $index;

    // Check for other matching departments
    for ($j = $index + 1; $j < count($allStudentData); $j++) {
        $dept2 = $allStudentData[$j];

        // Ensure departments have different courses and same student count
        if (
            $dept1['department'] !== $dept2['department'] &&
            $dept1['semester'] === $dept2['semester'] &&
            $dept1['totalStudent'] === $dept2['totalStudent']
        ) {
            $matchedDepartments[] = $dept2;
            $processedDepartments[] = $j;
        }
    }

    // If at least two departments match the criteria, merge them in zigzag order
    if (count($matchedDepartments) > 1) {
        $mergedStudents = [];
        $numStudents = $matchedDepartments[0]['totalStudent'];

        for ($i = 0; $i < $numStudents; $i++) {
            foreach ($matchedDepartments as $dept) {
                $mergedStudents[] = $dept['students'][$i];
            }
        }

        // Create new merged department
        $mergedGroups[] = [
            'department' => 'Merged',
            'semester' => $dept1['semester'],
            'course' => 'Mixed',
            'totalStudent' => count($mergedStudents),
            'students' => $mergedStudents
        ];
    }
}
?>


<?php
// Read the JSON file
$jsonString = file_get_contents('rooms.json');
$dataArray = json_decode($jsonString, true);
// echo count($mergedGroups);
?>

<div class="container p-3">

        <?php

        $room_no = 0;
        // Define department colors (you can adjust these as needed)
        $departmentColors = [
            1 => 'lightblue',  // Department 1 (example: blue)
            2 => 'lightgreen',  // Department 2 (example: green)
            3 => 'lightcoral',  // Department 3 (example: coral)
            // Add more departments and their colors as needed
        ];

        if (isset($dataArray['room']) && isset($mergedGroups)) {
            echo '<div class="row g-4">';
            // Loop through rooms and assign students from mergedGroups to each room
            foreach ($dataArray['room'] as $roomIndex => $room) {
                // If mergedGroups has only one group, break after first loop
                if (count($mergedGroups) == $roomIndex) {
                    // $room_no++;
                    // remainder
                    break;
                } else {
                    // Ensure the room corresponds to the right merged group (if there are enough merged groups)
                    if (isset($mergedGroups[$roomIndex])) {
                        $studentsInRoom = $mergedGroups[$roomIndex]['students'];  // Get the students for the current room
                    } else {
                        // If there are more rooms than merged groups, you can either skip or handle accordingly
                        $studentsInRoom = [];
                    }

                    // Calculate the number of rows
                    $numRows = ceil($room['seat_capacity'] / $room['bench_order']);

                    // Output the room details
                    echo '<div class="col-12">';
                    echo '<h5>Room No: ' . htmlspecialchars($room['room_no']) . ' - ' . htmlspecialchars($room['room_name']) . '</h5>';
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';

                    // Create bench order columns
                    for ($i = 1; $i <= $room['bench_order']; $i++) {
                        echo '<th>Bench ' . $i . '</th>';
                    }

                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    // Create rows for each bench row
                    $studentIndex = 0;  // Track the student index for the current room

                    for ($r = 0; $r < $numRows; $r++) {
                        echo '<tr>';

                        // Determine row order: Even rows (L -> R), Odd rows (R -> L)
                        $isLeftToRight = ($r % 2 == 0);
                        $rowSeats = [];

                        for ($b = 0; $b < $room['bench_order']; $b++) {
                            // Calculate seat number
                            $seatNumber = $r * $room['bench_order'] + $b + 1;

                            if ($seatNumber <= $room['seat_capacity']) {
                                // Assign students for the seat (2 per seat)
                                $studentsForSeat = [];

                                for ($i = 0; $i < 2; $i++) {
                                    if ($studentIndex < count($studentsInRoom)) {
                                        $studentsForSeat[] = $studentsInRoom[$studentIndex];
                                        $studentIndex++;
                                    }
                                }

                                // Swap students for odd rows (Zigzag effect)
                                if (!$isLeftToRight) {
                                    $studentsForSeat = array_reverse($studentsForSeat);
                                }

                                // Store seat info in array
                                $rowSeats[] = [
                                    'seatNumber' => $seatNumber,
                                    'students' => $studentsForSeat
                                ];
                            } else {
                                $rowSeats[] = null;  // Empty seat
                            }
                        }

                        // Print row seats
                        foreach ($rowSeats as $seat) {
                            echo '<td>';
                            if ($seat !== null) {
                                echo 'Seat ' . $seat['seatNumber'] . ':<br>';
                                foreach ($seat['students'] as $index => $student) {
                                    $position = ($index == 0) ? 'L' : 'R';  // Left or Right seat

                                    // Get department color
                                    $departmentColor = isset($departmentColors[$student['department']])
                                        ? $departmentColors[$student['department']]
                                        : 'lightgray';

                                    // Display student with color and position
                                    echo '<span style="background-color: ' . $departmentColor . '; 
                                        margin-right: 2px; padding: 2px; display: inline-block;">';
                                    echo $position . ': ' . htmlspecialchars($student['roll_no']) . '</span>';
                                }
                            }
                            echo '</td>';
                        }

                        echo '</tr>';
                    }

                    // echo '</table>';

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
                $room_no++;
            }
            echo '</div>';
        }
        ?>
    </div>    


<?php

?>


<?php

$directoryRemainder = 'database/departments/data';  // Define the base directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directoryRemainder),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$remainderStudentData = [];  // Array to store all student data

foreach ($iterator as $fileInfo) {
    if ($fileInfo->isFile() && $fileInfo->getExtension() === 'json') {
        $filename = $fileInfo->getPathname();
        $jsonString = file_get_contents($filename);
        $studentData = json_decode($jsonString, true);
        if ($studentData !== null) {
            $remainderStudentData[] = $studentData;
        } else {
            echo "Error decoding JSON from file: $filename\n";
        }
    }
}
usort($remainderStudentData, function ($a, $b) {
    return $b['totalStudent'] <=> $a['totalStudent'];
});

echo '<pre>';
$countRemainder = $remainderSeatY->findTotal();
$retirveRemainder = $remainderSeatY->findAll();
$retriveFinalRemainder = $departmentsStore->findAll();

if (count($retirveRemainder) == 0) {
    // remainder department 0
    print_r(count($retirveRemainder));
    if ($retriveFinalRemainder[0]['totalStudent'] != 0) {
        $usedRoomNos = array_column($dataArray["room"], 'room_no');
        // Step 2: Filter out any matching room from $rooms
        $filteredRooms = array_filter($rooms, function($room) use ($usedRoomNos) {
            return !in_array($room['room_no'], $usedRoomNos);
        });

        // Step 3: Reindex the array (optional)
        $filteredRooms = array_values($filteredRooms);

        $findKroom = findNearestRoomS($filteredRooms,$retriveFinalRemainder[0]['totalStudent']);
        // print_r($retriveFinalRemainder);
        // print_r($findKroom);

        $departmentColors = [
            1 => 'lightblue',
            2 => 'lightgreen',
            3 => 'lightcoral',
            5 => 'lightpink',
            // Add more if needed
        ];
        
        $remainderRoom = $findKroom['room'][0]; // assuming it's a single-room array
        $students = $retriveFinalRemainder[0]['students']; // using first group of students
        
        $totalSeats = $remainderRoom['seat_capacity']; // total seat count
        $benchesPerRow = $remainderRoom['bench_order'];
        $numRows = ceil($totalSeats / $benchesPerRow);
        
        echo '<div class="container">';
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<h5>Room No: ' . htmlspecialchars($remainderRoom['room_no']) . ' - ' . htmlspecialchars($remainderRoom['room_name']) . '</h5>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr>';
        
        for ($i = 1; $i <= $benchesPerRow; $i++) {
            echo '<th>Bench ' . $i . '</th>';
        }
        
        echo '</tr></thead><tbody>';
        
        $studentIndex = 0;
        $seatNumber = 1;
        
        for ($row = 0; $row < $numRows; $row++) {
            echo '<tr>';
            $isLeftToRight = ($row % 2 == 0);
        
            for ($bench = 0; $bench < $benchesPerRow; $bench++) {
                $benchIndex = $isLeftToRight ? $bench : ($benchesPerRow - 1 - $bench);
        
                echo '<td>';
        
                if ($studentIndex < count($students)) {
                    $student = $students[$studentIndex];
                    $position = ($benchIndex % 2 == 0) ? 'L' : 'R'; // Zigzag logic
                    $deptColor = $departmentColors[$student['department']] ?? 'lightgray';
        
                    echo '<span style="background-color:' . $deptColor . ';
                        display:inline-block; padding:4px; margin:2px;">';
                    echo $position . ' (Seat ' . $seatNumber . '): ' . htmlspecialchars($student['roll_no']);
                    echo '</span>';
        
                    $studentIndex++;
                    $seatNumber++;
                } else {
                    echo '&nbsp;';
                }
        
                echo '</td>';
            }
        
            echo '</tr>';
        }
        
        echo '</tbody></table></div></div></div>';
    }
    else {

    }

} else {
    // remainder same department 1
    print_r(count($retriveFinalRemainder));

    $combinedData = array_merge($retirveRemainder, $retriveFinalRemainder);
    // Sort combined array in descending order by 'totalStudent'
    usort($combinedData, function ($a, $b) {
        return $b['totalStudent'] <=> $a['totalStudent'];
    });

    if (
        $combinedData[0]['department'] == $combinedData[1]['department'] &&
        $combinedData[0]['semester'] == $combinedData[1]['semester'] &&
        $combinedData[0]['course'] == $combinedData[1]['course']
    ) {
        echo 'Same department, semester, and course.';
    } else {
        echo 'Different department, semester, or course.';
        // print_r($FinalRemainderVar);
        $FinalRemainderVar = buildFinalArrayX($combinedData[0], $combinedData[1]);
        
        $stdToMinX = min(count($combinedData[0]["students"]), count($combinedData[1]["students"]));
        $stdToVarX = array_slice($combinedData[0]["students"], $stdToMinX);

        // print_r($stdToVarX );
        // print_r($dataArray);
        // print_r($rooms);
        // Step 1: Extract used room numbers from nested "room" array
        $usedRoomNos = array_column($dataArray["room"], 'room_no');
        // Step 2: Filter out any matching room from $rooms
        $filteredRooms = array_filter($rooms, function($room) use ($usedRoomNos) {
            return !in_array($room['room_no'], $usedRoomNos);
        });

        // Step 3: Reindex the array (optional)
        $filteredRooms = array_values($filteredRooms);

        // Final output
        // print_r($filteredRooms);

        usort($FinalRemainderVar, function ($a, $b) {
            return $b['totalStudent'] <=> $a['totalStudent'];
        });

        // print_r($FinalRemainderVar);
        $mergedGroupsX = mergeSameSemesterEqualStudentDepartments($FinalRemainderVar);
        ?>
        <div class="container p-3">
        <?php
        // echo $room_no;
        $departmentColors = [
            1 => 'lightblue',
            2 => 'lightgreen',
            3 => 'lightcoral',
            5 => 'lightpink',
            // Add more department => color as needed
        ];
        $remainderRoom = $dataArray['room'][$room_no];
        // Get the specific room
        $studentsInRoom = $mergedGroupsX[0];  // Full merged group (with 'students' key)

        $students = $studentsInRoom['students'];  // Only students array

        $numRows = ceil($remainderRoom['seat_capacity'] / $remainderRoom['bench_order']);

        echo '<div class="col-12">';
        echo '<h5>Room No: ' . htmlspecialchars($remainderRoom['room_no']) . ' - ' . htmlspecialchars($room['room_name']) . '</h5>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr>';

        for ($i = 1; $i <= $remainderRoom['bench_order']; $i++) {
            echo '<th>Bench ' . $i . '</th>';
        }

        echo '</tr></thead><tbody>';

        $studentIndex = 0;

        for ($r = 0; $r < $numRows; $r++) {
            echo '<tr>';
            $isLeftToRight = ($r % 2 == 0);
            $rowSeats = [];

            for ($b = 0; $b < $remainderRoom['bench_order']; $b++) {
                $seatNumber = $r * $remainderRoom['bench_order'] + $b + 1;

                if ($seatNumber <= $remainderRoom['seat_capacity']) {
                    $studentsForSeat = [];

                    for ($i = 0; $i < 2; $i++) {
                        if ($studentIndex < count($students)) {
                            $studentsForSeat[] = $students[$studentIndex];
                            $studentIndex++;
                        }
                    }

                    if (!$isLeftToRight) {
                        $studentsForSeat = array_reverse($studentsForSeat);
                    }

                    $rowSeats[] = [
                        'seatNumber' => $seatNumber,
                        'students' => $studentsForSeat
                    ];
                } else {
                    $rowSeats[] = null;
                }
            }

            foreach ($rowSeats as $seat) {
                echo '<td>';
                if ($seat !== null) {
                    echo 'Seat ' . $seat['seatNumber'] . ':<br>';
                    foreach ($seat['students'] as $index => $student) {
                        $position = ($index == 0) ? 'L' : 'R';
                        $deptColor = $departmentColors[$student['department']] ?? 'lightgray';

                        echo '<span style="background-color: ' . $deptColor . ';
                            margin: 2px; padding: 2px; display: inline-block;">';
                        echo $position . ': ' . htmlspecialchars($student['roll_no']) . '</span>';
                    }
                }
                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</tbody></table></div>';

        ?>        
        </div>
        <?php

        $updateNewRoomRemainder =  findNearestRoomS($filteredRooms,count($stdToVarX));
        // print_r($updateNewRoomRemainder);
        // print_r($stdToVarX);
        $departmentColors = [
            1 => 'lightblue',
            2 => 'lightgreen',
            3 => 'lightcoral',
            5 => 'lightpink',
            // Add more if needed
        ];
        
        $remainderRoom = $dataArray['room'][$room_no];
        $students = $stdToVarX; // Student list
        
        $totalSeats = $remainderRoom['seat_capacity']; // total seat count = how many students max
        $benchesPerRow = $remainderRoom['bench_order'];
        $numRows = ceil($totalSeats / $benchesPerRow);
        echo '<div class="container">';
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<h5>Room No: ' . htmlspecialchars($remainderRoom['room_no']) . ' - ' . htmlspecialchars($remainderRoom['room_name']) . '</h5>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr>';
        
        for ($i = 1; $i <= $benchesPerRow; $i++) {
            echo '<th>Bench ' . $i . '</th>';
        }
        
        echo '</tr></thead><tbody>';
        
        $studentIndex = 0;
        $seatNumber = 1;
        
        for ($row = 0; $row < $numRows; $row++) {
            echo '<tr>';
            $isLeftToRight = ($row % 2 == 0);
        
            for ($bench = 0; $bench < $benchesPerRow; $bench++) {
                // Zigzag: even row => bench 0 → N, odd row => N → 0
                $benchIndex = $isLeftToRight ? $bench : ($benchesPerRow - 1 - $bench);
        
                echo '<td>';
        
                if ($studentIndex < count($students)) {
                    $student = $students[$studentIndex];
                    $position = ($benchIndex % 2 == 0) ? 'L' : 'R'; // Even bench = L, Odd = R
                    $deptColor = $departmentColors[$student['department']] ?? 'lightgray';
        
                    echo '<span style="background-color:' . $deptColor . ';
                        display:inline-block; padding:4px; margin:2px;">';
                    echo $position . ' (Seat ' . $seatNumber . '): ' . htmlspecialchars($student['roll_no']);
                    echo '</span>';
        
                    $studentIndex++;
                    $seatNumber++;
                } else {
                    echo '&nbsp;';
                }
        
                echo '</td>';
            }
        
            echo '</tr>';
        }
        
        echo '</tbody></table></div></div></div>';
                
    }
}

// print_r($FinalRemainderVar);
// $FinalRemainderVar = buildFinalArrayX($combinedData[0], $combinedData[1]);
// usort($FinalRemainderVar, function ($a, $b) {
//     return $b['totalStudent'] <=> $a['totalStudent'];
// });
// print_r($FinalRemainderVar);
// $mergedGroupsX = mergeSameSemesterEqualStudentDepartments($FinalRemainderVar);

// Print merged groups
// echo "<pre>";
// print_r($mergedGroupsX[0]);
// print_r($dataArray['room'][$room_no]);

// $mergedGroupsXIndexed = [];

// foreach ($dataArray['room'] as $i => $room) {
//     if (isset($mergedGroupsX[$i])) {
//         $mergedGroupsXIndexed[$room['room_no']] = $mergedGroupsX[$i];
//     }
// }
// print_r($mergedGroupsXIndexed);
// echo "</pre>";

// print_r($retirveRemainder);
// print_r($retriveFinalRemainder);
// print_r($dataArray);
// print_r($mergedGroups);
// print_r($remainderStudentData);
// print_r($rooms);
?>



<?php
function mergeSameSemesterEqualStudentDepartments(array $allStudentData): array
{
    $mergedGroups = [];
    $processedDepartments = [];

    foreach ($allStudentData as $index => $dept1) {
        if (in_array($index, $processedDepartments)) {
            continue;  // Skip already processed
        }

        $matchedDepartments = [$dept1];
        $processedDepartments[] = $index;

        for ($j = $index + 1; $j < count($allStudentData); $j++) {
            $dept2 = $allStudentData[$j];

            if (
                $dept1['department'] !== $dept2['department'] &&
                $dept1['semester'] === $dept2['semester'] &&
                $dept1['totalStudent'] === $dept2['totalStudent']
            ) {
                $matchedDepartments[] = $dept2;
                $processedDepartments[] = $j;
            }
        }

        // Only merge if at least 2 departments matched
        if (count($matchedDepartments) > 1) {
            $mergedStudents = [];
            $numStudents = $matchedDepartments[0]['totalStudent'];

            for ($i = 0; $i < $numStudents; $i++) {
                foreach ($matchedDepartments as $dept) {
                    $mergedStudents[] = $dept['students'][$i];
                }
            }

            $mergedGroups[] = [
                'department' => 'Merged',
                'semester' => $dept1['semester'],
                'course' => 'Mixed',
                'totalStudent' => count($mergedStudents),
                'students' => $mergedStudents
            ];
        }
    }

    return $mergedGroups;
}
?>