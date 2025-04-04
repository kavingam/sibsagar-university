<?php
/*
// Function to generate the department key
function getDeptKey($dept) {
    return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
}

// Function to slice the student data from the first department based on the total students in the second department
function getDeptStudentSlice($firstDept, $secondDept) {
    return array_slice($firstDept["students"], 0, $secondDept["totalStudent"]);
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
    }, $studentSlice ?? $dept["students"]);

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
function buildFinalArrayX($firstDept, $secondDept) {
    $finalArray = [];
    
    // Get the student slice for the first department
    $varBiggestDeptSlice = getDeptStudentSlice($firstDept, $secondDept);
    
    // Build the final array for the first department
    $finalArray[] = buildDeptArray($firstDept, $varBiggestDeptSlice);
    
    // Build the final array for the second department
    $finalArray[] = buildDeptArray($secondDept);
    
    return $finalArray;
}

*/

function getDeptKey($dept) {
    return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
}

// Function to slice the student data from the first department based on the total students in the second department
function getDeptStudentSlice($firstDept, $secondDept) {
    return array_slice($firstDept["students"], 0, $secondDept["totalStudent"]);
}

// Function to build department information for each student
function buildDeptArray($dept, $studentSlice = null, $overrideTotal = null) {
    // For each student, include department, semester, and course information
    $students = array_map(function($student) use ($dept) {
        return [
            "roll_no" => $student["roll_no"],
            "name" => $student["name"],
            "department" => $dept["department"],
            "semester" => $dept["semester"],
            "course" => $dept["course"]
        ];
    }, $studentSlice ?? $dept["students"]);

    return [
        "department" => $dept["department"],
        "semester" => $dept["semester"],
        "course" => $dept["course"],
        "totalStudent" => $overrideTotal ?? $dept["totalStudent"], // Override totalStudent if provided
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
    
    // Build the final array for the first department, overriding totalStudent with secondDept's totalStudent
    $finalArray[] = buildDeptArray($firstDept, $varBiggestDeptSlice, $secondDept["totalStudent"]);
    
    // Build the final array for the second department with its own totalStudent
    $finalArray[] = buildDeptArray($secondDept);
    
    return $finalArray;
}

function buildFinalArrayX($firstDept, $secondDept) {
    $finalArray = [];
    
    // Get the student slice for the first department
    $varBiggestDeptSlice = getDeptStudentSlice($firstDept, $secondDept);
    
    // Build the final array for the first department, overriding totalStudent with secondDept's totalStudent
    $finalArray[] = buildDeptArray($firstDept, $varBiggestDeptSlice, $secondDept["totalStudent"]);
    
    // Build the final array for the second department with its own totalStudent
    $finalArray[] = buildDeptArray($secondDept);
    
    return $finalArray;
}

?>

<?php 

    // function deleteJsonFiles($directory) { // No reference needed
    //     foreach (glob($directory . "*.json") as $file) {
    //         unlink($file);
    //     }
    // }
    //  check the files ownership
    // ls -l /var/www/sibsagar-university/xyz/dev/database/test_connection/data/
    // sudo chown -R www-data:www-data /var/www/sibsagar-university/xyz/dev/database/test_connection/data/

    function deleteJsonFiles($directory) {
        // Ensure the directory exists
        if (!is_dir($directory)) {
            die("Error: Directory '$directory' does not exist.<br>");
        }
    
        // Get all JSON files in the directory
        $files = glob($directory . "*.json");
    
        // Check if there are any JSON files
        if (empty($files)) {
            // echo "No JSON files found in '$directory'.<br>";
            return;
        }
    
        // Delete each JSON file
        foreach ($files as $file) {
            if (unlink($file)) {
                // echo "Deleted: $file<br>";
            } else {
                // echo "Failed to delete: $file<br>";
            }
        }
    }
    
?>
<?php
$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';
$sleekdbPath = __DIR__ . '/sleekdb.php';
$sleekdbxPath = __DIR__ . '/sleekdbx.php';
$filePath = __DIR__ . '/rooms.json'; // Define the file path
require __DIR__ . '/debugs.php';

$RemoveJsonPathxx = __DIR__ . '/database/departments/data/';
$RemoveJsonPathxy =  __DIR__ . '/database/seatAllocationList/data/';
$TestingJsonPathyx = __DIR__ . '/database/test_connection/data/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // deleteJsonFiles($TestingJsonPathyx);
    deleteJsonFiles($RemoveJsonPathxx);
    deleteJsonFiles($RemoveJsonPathxy);    

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

    $stdObj = new SeatAllocation();
    $totalStudent =  $stdObj->getTotalStudents($tableData);

    $students = new Student();
    $fetchingSimilarity = [];
    
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'], 
            $data['semester'], 
            $data['course'], 
            $data['totalStudent'] // Ensure total students are passed as range
        );
    
        // Store results
        $fetchingSimilarity[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents // Store retrieved students
        ];
    }

    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    // echo "<br>Total Examinations Students : ".$totalStudent."</br>";
    // echo "<br>Seats Per Bench: " . $benchSeat ."</br>";
    

    $seatAllocationListStore = new CreateSeatAllocation();

    $seatAlloc = new  CreateSeatAllocation();
    $total = $seatAlloc->findTotal(); // This will return and print the total count

    // echo '<h1>'.$total.'</h1>';

    $studentSeatCounts = []; // Array to store computed values

    for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
        // Check if there is a valid pair
        if (isset($fetchingSimilarity[$i + 1])) {
            $studentSeatCounts[] = $fetchingSimilarity[$i + 1]['totalStudent'] * 2;
        } else {
            // Store the remainder when only one element is left
            $studentSeatCounts[] = $fetchingSimilarity[$i]['totalStudent'];
        }
    }
    
    $seatAllocate = findNearestRoomS($rooms, ceil($totalStudent / $benchSeat));
    // // Convert the array to JSON format with proper formatting
    $jsonData = json_encode($seatAllocate, JSON_PRETTY_PRINT);

    $seatAllocations = [];

    foreach ($studentSeatCounts as $totalStudent) {
        $targetCapacity = ceil($totalStudent / $benchSeat);
        $seatAllocations[] = findNearestRoomS($rooms, $targetCapacity);
    }
    
    // echo '<pre>';
    // print_r($seatAllocations);
    
    if ($jsonData === false) {
        die("JSON encoding error: " . json_last_error_msg()); // JSON encoding error
    }

    // Try to write to the file
    if (file_put_contents($filePath, $jsonData) === false) {
        die("Error: Unable to write to file $filePath. Check file permissions.");
    } else {
        // echo "Data successfully saved to rooms.json";
    }
    
    for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
        // Check if there is a pair
        if (isset($fetchingSimilarity[$i + 1])) {
            // echo "Pair: " . $fetchingSimilarity[$i] . " and " . $fetchingSimilarity[$i + 1] . "<br>";
            $finalArray = buildFinalArrayX($fetchingSimilarity[$i],$fetchingSimilarity[$i+1]);
            $seatAllocationListStore->bulkInsert($finalArray);
            // print_r($fetchingSimilarity[$i+1]);
        } else {
            // echo "Last element: " . $fetchingSimilarity[$i] . "\n";  // If the array has an odd number of elements
            // print_r($fetchingSimilarity[$i]);
            // $finalArray = buildFinalArray($fetchingSimilarity[$i]);
            // $seatAllocationListStore->bulkInsert($finalArray);

        }
    }




try {
    // Initialize the necessary objects
    $departmentsStore = new DepartmentStore();
    $departmentsStore = new AdvancedDepartmentStore($departmentsStore); 
    $departmentsStore->deleteCache();
    $departmentsStore->bulkInsert($fetchingSimilarity);
    $getTotalDepartment = $departmentsStore->findAll();
    $index = count($getTotalDepartment);

    for ($i = 0; $i < $index; $i++) {
        // echo '<br>Run - ' . $i . '</br>';

        // Refresh department list
        $getTotalDepartmentx = $departmentsStore->findAll();


        // Ensure there are at least two departments
        if (count($getTotalDepartmentx) < 2) {
            break;
        }
        // Sort departments in descending order based on total students
        usort($getTotalDepartmentx, function ($a, $b) {
            return $b['totalStudent'] - $a['totalStudent'];
        });

        // echo '<pre>';
        
        $firstDump = $getTotalDepartmentx[0];
        $secondDump = $getTotalDepartmentx[1];

        // Determine removable students
        $stdToDump = min(count($secondDump["students"]), count($firstDump["students"]));
        $stdToVar = array_slice($firstDump["students"], $stdToDump);

        // Prepare remaining students list
        $varRemainder = [
            [
                "department"   => $firstDump["department"],
                "semester"     => $firstDump["semester"],
                "course"       => $firstDump["course"],
                "totalStudent" => count($stdToVar),
                "students"     => $stdToVar
            ]
        ];
        
        // echo '<pre>';

        if (isset($firstDump["_id"]) && isset($secondDump["_id"])) {

            $deleted1 = $departmentsStore->deleteById($firstDump["_id"]);
            $deleted2 = $departmentsStore->deleteById($secondDump["_id"]);
            // echo "✅ Successfully deleted departments <br>";
            $departmentsStore->bulkInsert($varRemainder);

        }
        

        // echo '</pre>';
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

}
?>

<?php
function findNearestRoomS($rooms, $targetCapacity) {
    // Sort rooms by seat_capacity ascending
    usort($rooms, function($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    // Try to find the first room that meets or exceeds the targetCapacity
    foreach ($rooms as $room) {
        if ($room['seat_capacity'] >= $targetCapacity) {
            return [
                'room' => [$room],
                'adjustment' => "Single Room Assigned"
            ];
        }
    }

    // If no room meets the requirement, return the room with highest capacity (last one)
    $fallbackRoom = end($rooms);
    return [
        'room' => [$fallbackRoom],
        'adjustment' => "Single Room Assigned"
    ];
}

function findNearestRoomM($rooms, $targetCapacity) {
    // Sort rooms in ascending order of seat_capacity
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
                'adjustment' => (count($selectedRooms) === 1) ? "Single Room Assigned" : "Merged Multiple Rooms"
            ];
        }
    }

    // If target not reached, return selected rooms anyway (best effort)
    return [
        'room' => $selectedRooms,
        'adjustment' => "Assigned with Less Capacity (Only " . $totalSeats . " seats available)"
    ];
}


// function findNearestRoomBak($rooms, $targetCapacity) {
//     usort($rooms, function($a, $b) {
//         return $a['seat_capacity'] - $b['seat_capacity'];
//     });

//     $selectedRooms = [];
//     $totalSeats = 0;

//     foreach ($rooms as $room) {
//         $selectedRooms[] = $room;
//     }
//         $totalSeats += $room['seat_capacity'];

//         if ($totalSeats >= $targetCapacity) {
//             return [
//                 'room' => $selectedRooms,
//                 'adjustment' => (count($selectedRooms) == 1) ? "Single Room Assigned" : "Merged Multiple Rooms"
//             ];
//         }
//     }
//     return [
//         'room' => [],
//         'adjustment' => "No Suitable Room Found (Minimum seat capacity is " . $rooms[0]['seat_capacity'] . ")"
//     ];


require_once __DIR__ . '/layout/multiLayout.php'; 
require_once __DIR__ . '/layout/multiLayout.php';

?>

<?php
// Define the directory path
$jsonDirectoryPath = __DIR__ . '/database/departments/data/';

// Check if the directory exists
if (!is_dir($jsonDirectoryPath)) {
    die("Error: Directory not found.");
}

// Scan the directory for JSON files
$jsonFiles = glob($jsonDirectoryPath . '/*.json');

$allStudents = []; // Array to store all students

// Loop through each JSON file
foreach ($jsonFiles as $jsonFile) {
    $jsonContent = file_get_contents($jsonFile);
    $decodedData = json_decode($jsonContent, true);

    // Check if decoding was successful and contains students
    if ($decodedData !== null && isset($decodedData['students'])) {
        $allStudents = array_merge($allStudents, $decodedData['students']);
    } else {
        echo "Error decoding JSON from file: $jsonFile\n";
    }
}

// Define seat configuration
$bench_order = 4;  // Number of benches per row
$numRows = ceil(count($allStudents) / $bench_order);

// Print table header
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Seat No</th><th>Position</th><th>Student</th></tr>";

$studentIndex = 0;
for ($r = 0; $r < $numRows; $r++) {
    $isLeftToRight = ($r % 2 == 0); // Zigzag pattern: Even row L → R, Odd row R → L
    $rowSeats = [];

    for ($b = 0; $b < $bench_order; $b++) {
        $studentForSeat = null;

        if ($studentIndex < count($allStudents)) {
            $studentForSeat = $allStudents[$studentIndex];
            $studentIndex++;
        }

        // Alternate seat positions (L, R, L, R)
        $position = ($b % 2 == 0) ? "L" : "R";
        if (!$isLeftToRight) {
            $position = ($position === "L") ? "R" : "L";
        }

        $rowSeats[] = [
            'seatNumber' => ($r * $bench_order) + ($b + 1),
            'position' => $position,
            'student' => $studentForSeat
        ];
    }

    // Print row seats
    foreach ($rowSeats as $seat) {
        echo "<tr>";
        echo "<td>{$seat['seatNumber']}</td>";
        echo "<td>{$seat['position']}</td>";
        echo "<td>";
        if ($seat['student']) {
            echo htmlspecialchars($seat['student']['roll_no']) . " (" . htmlspecialchars($seat['student']['name']) . ")";
        } else {
            echo "EMPTY";
        }
        echo "</td>";
        echo "</tr>";
    }
}

echo "</table>";
?>
