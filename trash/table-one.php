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
// print_r($dataArray);
echo '</pre>';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seating Arrangement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>


<!-- <body>
    <div class="container p-3"> -->
        <?php
        // if (isset($dataArray['room']) && isset($mergedGroups)) {
        //     echo '<div class="row g-4">';
        //     foreach ($dataArray['room'] as $room) {
        //         // Calculate the number of rows
        //         $numRows = ceil($room['seat_capacity'] / $room['bench_order']);
        //         $studentIndex = 0; // To track which student from mergedGroups is assigned to the seat
                
        //         // Output the room details
        //         echo '<div class="col-12">';
        //         echo '<h5>Room No: ' . htmlspecialchars($room['room_no']) . ' - ' . htmlspecialchars($room['room_name']) . '</h5>';
        //         echo '<table class="table table-bordered">';
        //         echo '<thead>';
        //         echo '<tr>';
                
        //         // Create bench order columns
        //         for ($i = 1; $i <= $room['bench_order']; $i++) {
        //             echo '<th>Bench ' . $i . '</th>';
        //         }
                
        //         echo '</tr>';
        //         echo '</thead>';
        //         echo '<tbody>';
                
        //         // Create rows for each bench row
        //         for ($r = 0; $r < $numRows; $r++) {
        //             echo '<tr>';
        //             for ($b = 0; $b < $room['bench_order']; $b++) {
        //                 // Calculate seat number
        //                 $seatNumber = $r * $room['bench_order'] + $b + 1;
        //                 if ($seatNumber <= $room['seat_capacity']) {
        //                     // If there are students left in the mergedGroups, assign them to seats
        //                     $studentsForSeat = []; // Array to hold two students per seat
                            
        //                     // Assign two students to the seat if possible
        //                     for ($i = 0; $i < 2; $i++) {
        //                         if ($studentIndex < count($mergedGroups[0]['students'])) {
        //                             $studentsForSeat[] = $mergedGroups[0]['students'][$studentIndex];
        //                             $studentIndex++;
        //                         }
        //                     }

        //                     // Display seat with assigned students
        //                     echo '<td>';
        //                     echo 'Seat ' . $seatNumber . ':<br>';
        //                     foreach ($studentsForSeat as $student) {
        //                         echo htmlspecialchars($student['roll_no']) . '<br>';
        //                     }
        //                     echo '</td>';
        //                 } else {
        //                     echo '<td></td>'; // Empty cell if the seat number exceeds seat capacity
        //                 }
        //             }
        //             echo '</tr>';
        //         }

        //         echo '</tbody>';
        //         echo '</table>';
        //         echo '</div>';
        //     }
        //     echo '</div>';
        // }
        ?>
    <!-- </div>    
</body> -->




<body>
    <div class="container p-3">
        <?php
        // Define department colors (you can adjust these as needed)
        $departmentColors = [
            1 => 'lightblue',   // Department 1 (example: blue)
            2 => 'lightgreen',  // Department 2 (example: green)
            3 => 'lightcoral',  // Department 3 (example: coral)
            // Add more departments and their colors as needed
        ];

        if (isset($dataArray['room']) && isset($mergedGroups)) {
            echo '<div class="row g-4">';
            
            // Loop through rooms and assign students from mergedGroups to each room
            foreach ($dataArray['room'] as $roomIndex => $room) {
                // Ensure the room corresponds to the right merged group (if there are enough merged groups)
                if (isset($mergedGroups[$roomIndex])) {
                    $studentsInRoom = $mergedGroups[$roomIndex]['students']; // Get the students for the current room
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
                $studentIndex = 0; // Track the student index for the current room
                for ($r = 0; $r < $numRows; $r++) {
                    echo '<tr>';
                    for ($b = 0; $b < $room['bench_order']; $b++) {
                        // Calculate seat number
                        $seatNumber = $r * $room['bench_order'] + $b + 1;
                        if ($seatNumber <= $room['seat_capacity']) {
                            // Array to hold students for the seat (2 students per seat)
                            $studentsForSeat = [];
                            
                            // Assign two students to the seat if possible
                            for ($i = 0; $i < 2; $i++) {
                                if ($studentIndex < count($studentsInRoom)) {
                                    $studentsForSeat[] = $studentsInRoom[$studentIndex];
                                    $studentIndex++;
                                }
                            }

                            // Display seat with assigned students
                            echo '<td>';
                            echo 'Seat ' . $seatNumber . ':<br>';
                            foreach ($studentsForSeat as $student) {
                                // Get department color from the $departmentColors array
                                $departmentColor = isset($departmentColors[$student['department']]) ? $departmentColors[$student['department']] : 'lightgray';

                                // Display each student inline with department color and 2px gap
                                echo '<span style="background-color: ' . $departmentColor . '; margin-right: 2px; padding: 2px;">';
                                echo htmlspecialchars($student['roll_no']) . '</span>';
                            }
                            echo '</td>';
                        } else {
                            echo '<td></td>'; // Empty cell if the seat number exceeds seat capacity
                        }
                    }
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>    
</body>


</html>


