<?php
require_once '../bashmodel.php';
require_once '../seat_allocation/seat_allocation.php';
require_once 'sleekdb.php';
require_once 'sleekdbx.php';

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
    //print_r($tableData); // Department Details With Desending to Assending Order
    echo "<br>";
    // print_r($fetchingSimilarity); // Selected Data Fetching And Retrive Details
    
    $dept = [
        [
            "department" => 1,
            "semester" => 1,
            "course" => 1,
            "totalStudent" => 10,
            "students" => array_map(function ($i) {
                return [
                    "roll_no" => "ASS-UG-SEM-10" . str_pad($i, 2, "0", STR_PAD_LEFT),
                    "name" => "AA-" . str_pad($i, 2, "0", STR_PAD_LEFT),
                    "department" => 1,
                    "semester" => 1,
                    "course" => 1
                ];
            }, range(1, 10))
        ],
        [
            "department" => 3,
            "semester" => 1,
            "course" => 1,
            "totalStudent" => 8,
            "students" => array_map(function ($i) {
                return [
                    "roll_no" => "CSE-SEM-10" . str_pad($i, 2, "0", STR_PAD_LEFT),
                    "name" => "AA-" . str_pad($i, 2, "0", STR_PAD_LEFT),
                    "department" => 3,
                    "semester" => 1,
                    "course" => 1
                ];
            }, range(1, 8))
        ]
    ];
    
    $x = $dept[0];
    $y = $dept[1];
    $groupSeatList = array_slice($x["students"], 0, $y["totalStudent"]);
    echo '<pre>';    
    // print_r($groupSeatList);
    $filtersGroupSeatList = [
        [
            [
                "department" => $x["department"],
                "semester" => $x["semester"],
                "course" => $x["course"],
                "totalStudent" => count($groupSeatList), // Updated count
                "students" => $groupSeatList
            ],
            [
                "department" => $y["department"],
                "semester" => $y["semester"],
                "course" => $y["course"],
                "totalStudent" => count($groupSeatList), // Updated count
                "students" => $groupSeatList
            ],
        ]
    ];
    print_r($filtersGroupSeatList);
    $seatAllocationListStore = new SeatAllocationList();

    // $departmentsStore->deleteCache();


/*

    try {
        $departmentsStore = new DepartmentStore();
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
*/
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