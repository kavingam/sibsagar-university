<?php
$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';
$sleekdbPath = __DIR__ . '/sleekdb.php';
$sleekdbxPath = __DIR__ . '/sleekdbx.php';

if (!file_exists($bashmodelPath)) {
    die("Error: bashmodel.php not found at: $bashmodelPath");
}
require_once $bashmodelPath;

if (!file_exists($seatAllocationPath)) {
    die("Error: seat_allocation.php not found at: $seatAllocationPath");
}
require_once $seatAllocationPath;

if (!file_exists($sleekdbPath)) {
    die("Error: sleekdb.php not found at: $sleekdbPath");
}
require_once $sleekdbPath;

if (!file_exists($sleekdbxPath)) {
    die("Error: sleekdbx.php not found at: $sleekdbPathx");
}
require_once $sleekdbxPath;



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

    $seatAllocate = findNearestRoom($rooms, ceil($totalStudent / $benchSeat));
    

    echo "<br>Total Examinations Students : ".$totalStudent;
    echo "<br>Seats Per Bench: " . $benchSeat ."<br>";
    
    // echo "<pre>";
    // print_r($tableData);
    // echo "<br>";
    // print_r($fetchingSimilarity); 

try {
    // Initialize the necessary objects
    $departmentsStore = new DepartmentStore();
    $departmentsStore = new AdvancedDepartmentStore($departmentsStore);
    $seatAllocationListStore = new CreateSeatAllocation();

    echo "✅ Connection Successful!<br>";
    
    // Clear cache
    $departmentsStore->deleteCache();

    // Bulk insert department data
    $departmentsStore->bulkInsert($fetchingSimilarity);

    // Retrieve all departments
    $getTotalDepartment = $departmentsStore->findAll();
    $index = count($getTotalDepartment);

    for ($i = 0; $i < $index; $i++) {
        echo 'Run - ' . $i . '<br>';

        // Refresh department list
        $getTotalDepartmentx = $departmentsStore->findAll();

        // Sort departments in descending order based on total students
        usort($getTotalDepartmentx, function ($a, $b) {
            return $b['totalStudent'] - $a['totalStudent'];
        });

        $firstDump = $getTotalDepartmentx[0];
        $secondDump = $getTotalDepartmentx[1];

        // Ensure there are at least two departments
        if (count($getTotalDepartmentx) < 2) {
            // $mergedDepartments = mergeDepartments($firstDump, $secondDump);
            // $seatAllocationListStore->bulkInsert($mergedDepartments);
            break;
        }


        $mergedDepartments = mergeDepartments($firstDump, $secondDump);
        $seatAllocationListStore->bulkInsert($mergedDepartments);
        echo '<pre>';
        print_r($mergedDepartments);
        // Merging department start

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
        
        echo '<pre>';

        if (isset($firstDump["_id"]) && isset($secondDump["_id"])) {
            // Delete both records by _id
            $deleted1 = $departmentsStore->deleteById($firstDump["_id"]);
            $deleted2 = $departmentsStore->deleteById($secondDump["_id"]);

            echo "✅ Successfully deleted departments <br>";

            // Insert merged and remaining students into the store
            $departmentsStore->bulkInsert($varRemainder);
        }
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
function mergeDepartments($firstDump, $secondDump) {
    // Ensure both departments exist
    if (!$firstDump || !$secondDump) {
        return [];
    }

    // Slice students from the first department based on the second department's total students
    $requiredStudents = array_slice($firstDump["students"], 0, $secondDump["totalStudent"]);

    // Remaining students from the first department
    $remainingStudents = array_slice($firstDump["students"], $secondDump["totalStudent"]);

    // Store merged data
    $mergedDepartments = [];

    // First department (remaining students)
    if (!empty($remainingStudents)) {
        $mergedDepartments[] = [
            "department"   => $firstDump["department"],
            "semester"     => $firstDump["semester"],
            "course"       => $firstDump["course"],
            "totalStudent" => count($remainingStudents), 
            "students"     => $remainingStudents
        ];
    }

    // Second department remains unchanged
    $mergedDepartments[] = [
        "department"   => $secondDump["department"],
        "semester"     => $secondDump["semester"],
        "course"       => $secondDump["course"],
        "totalStudent" => count($secondDump["students"]), // Keep its original count
        "students"     => $secondDump["students"] // Keep original students
    ];

    return $mergedDepartments;
}

?>