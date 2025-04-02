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
$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';
$sleekdbPath = __DIR__ . '/sleekdb.php';
$sleekdbxPath = __DIR__ . '/sleekdbx.php';
// $layout_path = __DIR__ . '/layout/xyz_layout.php';

require __DIR__ . '/debugs.php';



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

    // $seatAllocate = findNearestRoom($rooms, ceil($totalStudent / $benchSeat));
    

    echo "<br>Total Examinations Students : ".$totalStudent."</br>";
    echo "<br>Seats Per Bench: " . $benchSeat ."</br>";
    

    $seatAllocationListStore = new CreateSeatAllocation();

    $seatAlloc = new  CreateSeatAllocation();
    $total = $seatAlloc->findTotal(); // This will return and print the total count
    
    echo '<h1>'.$total.'</h1>';

    // echo '<pre>';
    for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
        // Check if there is a pair
        if (isset($fetchingSimilarity[$i + 1])) {
            // echo "Pair: " . $fetchingSimilarity[$i] . " and " . $fetchingSimilarity[$i + 1] . "\n";
            $finalArray = buildFinalArrayX($fetchingSimilarity[$i],$fetchingSimilarity[$i+1]);
            $seatAllocationListStore->bulkInsert($finalArray);
            // print_r($finalArray);
        } else {
            // echo "Last element: " . $fetchingSimilarity[$i] . "\n";  // If the array has an odd number of elements
            // print_r($fetchingSimilarity[$i]);
            // $finalArray = buildFinalArray($fetchingSimilarity[$i]);
            // $seatAllocationListStore->bulkInsert($finalArray);

        }
    }

    // Now you can call the function
    // $finalArray = buildFinalArray($fetchingSimilarity);
    // print_r($finalArray);
    // print_r($tableData);
    
    // print_r($fetchingSimilarity); 
    // print_r($seatAllocate);

try {
    // Initialize the necessary objects
    $departmentsStore = new DepartmentStore();
    $departmentsStore = new AdvancedDepartmentStore($departmentsStore); 
    $departmentsStore->deleteCache();
    $departmentsStore->bulkInsert($fetchingSimilarity);
    $getTotalDepartment = $departmentsStore->findAll();
    $index = count($getTotalDepartment);

    for ($i = 0; $i < $index; $i++) {
        echo '<br>Run - ' . $i . '</br>';

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
function findNearestRoom($rooms, $targetCapacity) {
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
    return [
        'room' => [],
        'adjustment' => "No Suitable Room Found (Minimum seat capacity is " . $rooms[0]['seat_capacity'] . ")"
    ];
}?>

<?php 

require_once __DIR__ . '/layout/multiLayout.php'; 

?>