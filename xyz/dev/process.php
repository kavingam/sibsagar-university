<?php

$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';
$sleekdbPath = __DIR__ . '/sleekdb.php';
$sleekdbxPath = __DIR__ . '/sleekdbx.php';
$sleekdbyPath = __DIR__ . '/sleekdby.php';
$sleekdbxxPath = __DIR__ . '/sleekdbxx.php';

$filePath = __DIR__ . '/rooms.json';
require __DIR__ . '/debugs.php';

$RemoveJsonPathxx = __DIR__ . '/database/departments/data/';
$RemoveJsonPathxy = __DIR__ . '/database/seatAllocationList/data/';
$RemoveJsonPathyx = __DIR__ . '/database/RemainderStudent/data/';
$TestingJsonPathyx = __DIR__ . '/database/test_connection/data/';

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

    // students info
    $students = new Student();
    $fetchingSimilarity = [];
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['semester'],
            $data['course'],
            $data['totalStudent']
        );

        // Store results
        $fetchingSimilarity[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    // total students
    $stdObj = new SeatAllocation();
    $totalStudent = $stdObj->getTotalStudents($tableData);

    // rooms
    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    // $fetchingSimilarity;
    // $totalStudent;
    // $rooms;

    deleteJsonFiles($RemoveJsonPathxx);
    deleteJsonFiles($RemoveJsonPathxy);
    deleteJsonFiles($RemoveJsonPathyx);


    $seatAllocationListStore = new CreateSeatAllocation();
    $seatAlloc = new  CreateSeatAllocation();
    

    $remainderSeatX  = new RemainderStudent();
    $remainderSeatY  = new RemainderSeatAllocation();

    $deptData = null; // to hold the unpaired remainder if exists
    
    for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
        // If we have a pair
        if (isset($fetchingSimilarity[$i + 1])) {
            // if (count($fetchingSimilarity[$i]) == count($fetchingSimilarity[$i + 1])) {
            //     $seatAllocationListStore->bulkInsert($finalArray);
            // }
            $finalArray = buildFinalArrayX($fetchingSimilarity[$i], $fetchingSimilarity[$i + 1]);
            $seatAllocationListStore->bulkInsert($finalArray);
        } else {
            // Store the single unmatched department data
            $deptData = $fetchingSimilarity[$i];
            if (!empty($deptData)) {
                $remainderSeatY->bulkInsert([$deptData]);
            }
        }
    }

    // calculate total examination students
    $remainderValue = 0;        
        try {
            $studentSeatCounts = [];        
            for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
                if (isset($fetchingSimilarity[$i + 1])) {
                    $studentSeatCounts[] = $fetchingSimilarity[$i + 1]['totalStudent'] * 2;
                    $remainderValue = $fetchingSimilarity[$i + 1]['totalStudent'] - $fetchingSimilarity[$i]['totalStudent'];
                } else {
                    $studentSeatCounts[] = abs($remainderValue*2);
                }
            }
            $seatAllocations = [];

            // foreach ($studentSeatCounts as $totalStudent) {
                // $targetCapacity = ceil($totalStudent / $benchSeat);
                // $room = findNearestRoomS($rooms, $targetCapacity);

                // if ($room) {
                //     $seatAllocations[] = [
                //         'requiredSeats' => $totalStudent,
                //         'requiredCapacity' => $targetCapacity,
                //         'allocatedRoom' => $room
                //     ];
                // } else {
                //     $seatAllocations[] = [
                //         'requiredSeats' => $totalStudent,
                //         'requiredCapacity' => $targetCapacity,
                //         'allocatedRoom' => null,
                //         'error' => 'No matching room found'
                //     ];
                // }
            // }

            // foreach ($studentSeatCounts as $totalStudent) {
            //     $targetCapacity = ceil($totalStudent / $benchSeat);
            //     $room = findNearestRoomS($rooms, $targetCapacity);
            
            //     if ($room && isset($room['room'])) {
            //         foreach ($room['room'] as $singleRoom) {
            //             $seatAllocations[] = $singleRoom; // Flat structure: push only the room detail
            //         }
            //     }
            // }
            
            $flatRoomList = [];

            foreach ($studentSeatCounts as $totalStudent) {
                $targetCapacity = ceil($totalStudent / $benchSeat);
                $room = findNearestRoomS($rooms, $targetCapacity);
            
                if ($room && isset($room['room'])) {
                    foreach ($room['room'] as $singleRoom) {
                        $flatRoomList[] = $singleRoom;
                    }
                }
            }
            
            $seatAllocations = [
                'room' => $flatRoomList
            ];
            


            $jsonData = json_encode($seatAllocations, JSON_PRETTY_PRINT);
            if ($jsonData === false) {
                die("JSON encoding error: " . json_last_error_msg());
            }

            if (file_put_contents($filePath, $jsonData) === false) {
                die("Error: Unable to write to file $filePath. Check file permissions.");
            } else {
                /*
                 *   echo "Data successfully saved to rooms.json";
                 * 
                 */
            }


    } catch (Exception $e) {
        echo 'Room Allotment Error: ' . $e->getMessage();
    }




    // calculate total Student final remainder 
    try {
        $departmentsStore = new DepartmentStore();
        $departmentsStore = new AdvancedDepartmentStore($departmentsStore);

        $departmentsStore->bulkInsert($fetchingSimilarity);
        $getTotalDepartment = $departmentsStore->findAll();
        $getTotalDept = count($getTotalDepartment);

        for ($i = 0; $i < $getTotalDept; $i++) {

            $retriveTotalDept = $departmentsStore->findAll();

            if (count($retriveTotalDept) < 2) {
                break;
            }

            usort($retriveTotalDept, function ($a, $b) {
                return $b['totalStudent'] - $a['totalStudent'];
            });

            $firstDump = $retriveTotalDept[0];
            $secondDump = $retriveTotalDept[1];


            // if (count($firstDump) == count($secondDump)) {
                // print_r(count($firstDump));
                // $stdToVar =array_merge($firstDump['students'], $secondDump['students']);
                // continue;
            // } else {
                $stdToDump = min(count($secondDump['students']), count($firstDump['students']));
                $stdToVar = array_slice($firstDump['students'], $stdToDump);

            // }

            $varRemainder = [
                [
                    'department' => $firstDump['department'],
                    'semester' => $firstDump['semester'],
                    'course' => $firstDump['course'],
                    'totalStudent' => count($stdToVar),
                    'students' => $stdToVar
                ]
            ];

            if (isset($firstDump['_id']) && isset($secondDump['_id'])) {
                $deleted1 = $departmentsStore->deleteById($firstDump['_id']);
                $deleted2 = $departmentsStore->deleteById($secondDump['_id']);
                $departmentsStore->bulkInsert($varRemainder);
            }

        }
    } catch (Exception $e) {
        echo 'Error:  Remainder Seat Allote ' . $e->getMessage();
    }


    try {
        require_once __DIR__ . '/layout/multiLayout.php';
    } catch (Exception $e) {
        echo 'Error:  Room Examination Seat Allotement ' . $e->getMessage();
    }
}
?>

<?php
function deleteJsonFiles($directory)
{
    if (!is_dir($directory)) {
        die("Error: Directory '$directory' does not exist.<br>");
    }
    $files = glob($directory . '*.json');
    if (empty($files)) {
        return;
    }
    foreach ($files as $file) {
        if (unlink($file)) {
            // echo "Deleted: $file<br/>";
        } else {
            // echo "Failed to delete: $file<br/>";
        }
    }
}

function findNearestRoomS($rooms, $targetCapacity) {
    usort($rooms, function($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    foreach ($rooms as $room) {
        if ($room['seat_capacity'] >= $targetCapacity) {
            return [
                'room' => [$room],
                'adjustment' => "Single Room Assigned"
            ];
        }
    }
    $fallbackRoom = end($rooms);
    return [
        'room' => [$fallbackRoom],
        'adjustment' => "Single Room Assigned"
    ];
}
?>



<?php 
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