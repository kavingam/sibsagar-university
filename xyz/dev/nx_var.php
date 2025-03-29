<?php
$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';

if (!file_exists($bashmodelPath)) {
    die("Error: bashmodel.php not found at: $bashmodelPath");
}
require_once $bashmodelPath;

if (!file_exists($seatAllocationPath)) {
    die("Error: seat_allocation.php not found at: $seatAllocationPath");
}
require_once $seatAllocationPath;




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
        $departmentsStore = new DepartmentStore();
        $seatAllocationListStore = new CreateSeatAllocation();

        echo "✅ Connection Successful!<br>";
    
        $departmentsStore = new AdvancedDepartmentStore(new DepartmentStore($departmentsStore));
        $seatAllocationListStore = new SeatAllocationList();

        $departmentsStore->deleteCache();
        $departmentsStore->bulkInsert($fetchingSimilarity);

        $getTotalDepartment = $departmentsStore->findAll();
        

        $index = count($getTotalDepartment);

        for ($i = 0; $i < $index; $i++) {

            echo 'run - '.$i.'<br>';

            $getTotalDepartmentx = $departmentsStore->findAll();
            if (count($getTotalDepartmentx) == 1) {
                break;
            }
            usort($getTotalDepartmentx, function ($a, $b) {
                return $b['totalStudent'] - $a['totalStudent']; // Descending order
            });
    
            $firstDump = $getTotalDepartmentx[0]; 
            $secondDump = $getTotalDepartmentx[1];
    
            /* Merging department start*/

            $varAllSeat = [];
            function getDeptKey($dept) {
                return $dept["department"] . "-" . $dept["semester"] . "-" . $dept["course"];
            }

           // Store first department
            $key1 = getDeptKey($firstDept);
            $mergedDepartments[$key1] = [
                "department"   => $firstDept["department"],
                "semester"     => $firstDept["semester"],
                "course"       => $firstDept["course"],
                "totalStudent" => count($firstDept["students"]), // Keep original count
                "students"     => $firstDept["students"]        // Keep original students
            ];

            // Store second department with merged students
            $key2 = getDeptKey($secondDept);
            $mergedDepartments[$key2] = [
                "department"   => $secondDept["department"],
                "semester"     => $secondDept["semester"],
                "course"       => $secondDept["course"],
                "totalStudent" => count($requiredStudents) + count($secondDept["students"]), // Add students count
                "students"     => array_merge($requiredStudents, $secondDept["students"])   // Merge students properly
            ];
            
            /* Merging department end*/

            $stdToDump = min(count($secondDump["students"]), count($firstDump["students"])); // return removeable value
            $stdToVar = array_slice($firstDump["students"],$stdToDump);
    
            $varRemainder = [
                [
                    "department" => $firstDump["department"],
                    "semester" => $firstDump["semester"],
                    "course" => $firstDump["course"],
                    "totalStudent" => count($stdToVar),
                    "students" => $stdToVar
                ]
            ];
            
            echo '<pre>';
    
            if (isset($firstDump["_id"]) && isset($secondDump["_id"])) {
              // Delete both records by _id
                $deleted1 = $departmentsStore->deleteById($firstDump["_id"]);
                $deleted2 = $departmentsStore->deleteById($secondDump["_id"]);

                echo "successfully delete <br>";
                $departmentsStore->bulkInsert($varRemainder);
            }
        }

        
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
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