<?php 
/*
 *   Debug Version 0.1
 */
error_reporting(E_ALL);
ini_set('display_errors',1);


$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';

$remainderPath = __DIR__ . '/remainder_store.php';
$combinationPath = __DIR__ . '/combination_store.php';
$allocationPath = __DIR__ . '/seatallocation_store.php';

$draw_layoutPath = __DIR__ . '/layout/draw_layout.php';

$remove_cache_x = __DIR__ . '/json_database/combination_json/data/';
$remove_cache_y = __DIR__ . '/json_database/remainder_json/data/';
$remove_cache_a = __DIR__ . '/json_database/seatallocation_json/data/';

if (file_exists('debugs/debugs_logs.php')) {
    include_once 'debugs/debugs_logs.php';
} else {
    echo "Error: debugs/debugs_logs.php not found<br>";
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $examTime24 = htmlspecialchars($data['startTime']);
    $examTime = date("g:i A", strtotime($data['startTime'])); 
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $examName = htmlspecialchars($data['selectedExam']);
    $examDate = htmlspecialchars($data['startDate']);
    $saveData = htmlspecialchars($data['save']);

    // echo $saveData;

    $tableData = $data['tableData'];
    usort($tableData, function ($a, $b) {
        return $b['totalStudent'] <=> $a['totalStudent'];
    });

    $students = new Student();
    $fetchStudents = [];
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['semester'],
            $data['course'],
            $data['totalStudent']
        );

        $fetchStudents[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    $stdObj = new SeatAllocation();
    $totalStudent = $stdObj->getTotalStudents($tableData);
    $totalDept = count($tableData);

    $deptObj = new Department();
    $allDept = $deptObj->getAllDepartments();


    // print_r($allDept);
    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    $attendance = new AttendanceSheet();
    $dateTimes = $attendance->getAllAttendanceDateTime();

    $attendance->checkExamTimeConflict($examDate, $examTime);

    deleteJsonFiles($remove_cache_x);
    deleteJsonFiles($remove_cache_y);
    deleteJsonFiles($remove_cache_a);

    $remainderStore = new RemainderJSON();
    $remainderStore = new NewRemainderJSON($remainderStore);

    $seatAllocationStore = new SeatallocationJSON();
    $seatAllocationStore = new NewSeatallocationJSON($seatAllocationStore);

    $combinationStore = new CombinationJSON();
    $combinationStore = new NewCombinationJSON($combinationStore);

    $finalArrayX = []; // initialize before use

    echo '<pre>';
    // print_r($fetchStudents);
    $totalDepartments = calculateTotalDepartments($fetchStudents);
    echo "Total Unique Departments: " . $totalDepartments;

    echo '</pre>';

    for ($i = 0; $i < count($fetchStudents); $i += 2) {
        if (isset($fetchStudents[$i + 1])) {
            $firstDept = $fetchStudents[$i];
            $secondDept = $fetchStudents[$i + 1];
            $finalArrayX[] = buildFinalArrayX($firstDept, $secondDept);
        }
    }
    
    $combinationStore->bulkInsert($finalArrayX);
    $remainderStore->insertDepartmentsIfValid(pairSubtractRemainder($fetchStudents)['pairs']);
    $remainderStore->bulkInsert(pairSubtractRemainder($fetchStudents)['remainder']);
    
    $remainderArrayX = [];
    $trashArrayX = [];
    while (true) {
        // break the loop if remainder is 0, 1, or 2
        if ($remainderStore->findTotal() <= 1) {
            break; // <-- break here after printing
        } else {
            echo '<pre>';
            // print_r($remainderStore->findTotal());
            $remainderList = $remainderStore->findAll();
    
            usort($remainderList, function ($a, $b) {
                return $b['totalStudent'] <=> $a['totalStudent'];
            });
    
            $remainderArrayX[] = buildFinalArrayX($remainderList[0], $remainderList[1]);
            // $combinationStore->bulkInsert($remainderArrayX);

            $remainderStore->deleteByArray($remainderList[0]);
            $remainderStore->deleteByArray($remainderList[1]);

            // $remainderStore->insertDepartmentsIfValid(subtractRemainderOnly($remainderList[0], $remainderList[1]));
            echo '</pre>';           
            // break; 
        }
    }   

    $combinations_list = $combinationStore->findAll();
    $zigzagBlocks = getZigzagMergedStudents($combinations_list);
    
    foreach ($zigzagBlocks as $block) {
        // $seatAllocationStore->insertDept($block); // Store one block at a time
    }
    
   

    $groups = $remainderStore->findAll();
    
    foreach ($groups as $group) {
        // $block = transformGroupToBlockAuto($group);
    
        // Insert into database
        // $seatAllocationStore->insertDept($block);
    }
       

    require_once $draw_layoutPath;

}


?>

<?php
/** Testing Ok
 * Check if a number is even or odd.
 *
 * @param int $num The number to check.
 * @return int 1 if the number is even, 0 if it’s odd.
 */
function isEven(int $num): int {
    return ($num % 2 === 0) ? 1 : 0;
}

/** Testing Ok
 * Delete all JSON files in a given directory.
 *
 * @param string $directory The directory path to delete JSON files from.
 */
function deleteJsonFiles($directory) {
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
            echo "Failed to delete: $file<br/>";
        }
    }
}

/**
 * Computes student seat counts by pairing every two elements:
 * - For each pair, uses the second element’s totalStudent * 2
 * - If a lone element remains (odd length), uses its totalStudent
 *
 * @param array $blocks Array of associative arrays, each with a 'totalStudent' key.
 * @return int[]        Array of computed seat counts.
 */
function computeSeatCounts(array $blocks): array {
    $seatCounts = [];

    $n = count($blocks);
    for ($i = 0; $i < $n; $i += 2) {
        // If there is a next (paired) block...
        if (isset($blocks[$i + 1])) {
            $seatCounts[] = $blocks[$i + 1]['totalStudent'] * 2;
        } else {
            // No pair—take the last block’s own total
            $seatCounts[] = $blocks[$i]['totalStudent'];
        }
    }

    return $seatCounts;
}

/** Testing Ok
 * Pair up department blocks two‑by‑two, subtract their 'totalStudent' values,
 * and for each pair return a new block with:
 *   - department/semester/course (from the first block)
 *   - totalStudent (the subtraction result)
 *   - students        (the first block’s students, truncated to the new total)
 *
 * Any unpaired (odd) block is returned in 'remainder' exactly as‑is.
 *
 * @param array $items Indexed array of department‑blocks, each with:
 *                     department, semester, course, totalStudent, students (array)
 * @return array{pairs: array, remainder: array}
 */
function pairSubtractRemainder(array $items): array {
    $pairs     = [];
    $remainder = [];
    $n         = count($items);

    for ($i = 0; $i + 1 < $n; $i += 2) {
        $first  = $items[$i];
        $second = $items[$i + 1];

        // Ensure both have totalStudent and students:
        if (isset($first['totalStudent'], $second['totalStudent'], $first['students']) 
            && is_array($first['students'])) {
            
            // Compute new total
            $newTotal = $first['totalStudent'] - $second['totalStudent'];
            $newTotal = max(0, $newTotal); // avoid negative

            // Truncate the students list to the new total count
            $newStudents = array_slice($first['students'], -$newTotal);

            // Build the resulting block
            $pairs[] = [
                'department'   => $first['department'],
                'semester'     => $first['semester'],
                'course'       => $first['course'],
                'totalStudent' => $newTotal,
                'students'     => $newStudents,
            ];
        }
    }

    // Odd one out?
    if ($n % 2 !== 0) {
        $remainder[] = $items[$n - 1];
    }

    return [
        'pairs'     => $pairs,
        'remainder' => $remainder,
    ];
}

function pairSubtractRemainderY(array $department1, array $department2): array {
    $pairs     = [];
    $remainder = [];
    
    // The total count of students in both departments
    $n1 = count($department1);
    $n2 = count($department2);

    // Loop through both departments, pairing them
    while (count($department1) > 0 && count($department2) > 0) {
        // Take the first student from each department
        $first  = array_shift($department1); // Get the first student from department1
        $second = array_shift($department2); // Get the first student from department2

        // Ensure both have totalStudent and students
        if (isset($first['totalStudent'], $second['totalStudent'], $first['students']) 
            && isset($second['students']) && is_array($first['students']) && is_array($second['students'])) {
            
            // Compute new total
            $newTotal = $first['totalStudent'] - $second['totalStudent'];
            $newTotal = max(0, $newTotal); // avoid negative total

            // Truncate the students list to the new total count
            $newStudents = array_slice($first['students'], -$newTotal);

            // Build the resulting pair
            $pairs[] = [
                'department'   => $first['department'],
                'semester'     => $first['semester'],
                'course'       => $first['course'],
                'totalStudent' => $newTotal,
                'students'     => $newStudents,
            ];
        }
    }

    // Add remaining students to the remainder
    if (count($department1) > 0) {
        $remainder[] = array_shift($department1); // Remaining from department1
    }

    if (count($department2) > 0) {
        $remainder[] = array_shift($department2); // Remaining from department2
    }

    return [
        'pairs'     => $pairs,
        'remainder' => $remainder,
    ];
}

function subtractRemainderOnly(array $deptA, array $deptB): array {
    // Ensure both have 'totalStudent' and 'students'
    if (
        !isset($deptA['totalStudent'], $deptA['students']) ||
        !isset($deptB['totalStudent'], $deptB['students']) ||
        !is_array($deptA['students']) ||
        !is_array($deptB['students'])
    ) {
        return [];
    }

    // Calculate the new total after subtracting
    $newTotal = $deptA['totalStudent'] - $deptB['totalStudent'];
    $newTotal = max(0, $newTotal); // Ensure it's not negative

    // Get only the remaining students from the end
    $remainingStudents = array_slice($deptA['students'], -$newTotal);

    // Return only the remainder info
    return [[
        'department'   => $deptA['department'],
        'semester'     => $deptA['semester'],
        'course'       => $deptA['course'],
        'totalStudent' => $newTotal,
        'students'     => $remainingStudents,
    ]];
}

function allocateStudentsToRooms(array $rooms, int $total_students): array {
    $assigned_rooms = [];

    foreach ($rooms as $room) {
        if ($total_students <= 0) break;

        $room_capacity = $room['seat_capacity'] * 2; // 2 seats per bench
        $students_in_room = min($room_capacity, $total_students);

        $assigned_rooms[] = [
            "room_no" => $room['room_no'],
            "room_name" => $room['room_name'],
            "banch_order" => $room['bench_order'],
            "room_capacity" => $room['seat_capacity'],
            "students_assigned" => $students_in_room
        ];

        $total_students -= $students_in_room;
    }

    return $assigned_rooms;
}
   
function getZigzagMergedStudents($data) {
    $result = [];

    foreach ($data as $blockIndex => $block) {
        $departments = [];

        // Skip '_id' and collect students by department
        foreach ($block as $key => $group) {
            if ($key === '_id') continue;

            $deptId = $group['department'];
            foreach ($group['students'] as $student) {
                $departments[$deptId][] = $student;
            }
        }

        // Zigzag merge
        $zigzag = [];
        $max = max(array_map('count', $departments));

        for ($i = 0; $i < $max; $i++) {
            foreach ($departments as $students) {
                if (isset($students[$i])) {
                    $zigzag[] = $students[$i];
                }
            }
        }

        $result[] = [
            'block_id' => $block['_id'] ?? null,
            'zigzag_students' => $zigzag
        ];
    }

    return $result;
}

/**
 * Transform a group of students into a block format.
 *
 * @param array $group The group of students to transform.
 * @return array The transformed block with zigzag students.
 */
function transformGroupToBlockAuto($group) {
    // Automatically create block_id using _id + 300
    $blockId = isset($group['_id']) ? (int)$group['_id'] + 300 : rand(1000, 9999);

    $output = [
        'block_id' => $blockId,
        'zigzag_students' => []
    ];

    // Check if the group has students
    if (isset($group['students']) && is_array($group['students'])) {
        foreach ($group['students'] as $student) {
            // Add original student
            $output['zigzag_students'][] = [
                'roll_no' => (string)$student['roll_no'],
                'name' => (string)$student['name'],
                'department' => (string)$student['department'],
                'semester' => (string)$student['semester'],
                'course' => (string)$student['course'],
            ];

            // Add NIL student after the original student
            $output['zigzag_students'][] = [
                'roll_no' => 'NIL',
                'name' => 'NIL',
                'department' => 'NIL',
                'semester' => 'NIL',
                'course' => 'NIL',
            ];
        }
    }

    return $output;
}

function getDepartmentNameById($departments, $id) {
    foreach ($departments as $dept) {
        if ($dept['department_id'] == $id) {
            return $dept['department_name'];
        }
    }
    return null; // or return "Unknown Department";
}

function getSessionLabel($time) {
    $hour = date('H', strtotime($time)); // Convert to 24-hour format

    if ($hour < 12) {
        return 'Morning';
    } elseif ($hour >= 12 && $hour < 17) {
        return 'Afternoon';
    } else {
        return 'Evening';
    }
}

function calculateTotalDepartments($fetchStudents) {
    $uniqueDepartments = [];

    foreach ($fetchStudents as $entry) {
        // Unique key: department-semester-course
        $key = $entry['department'] . '-' . $entry['semester'] . '-' . $entry['course'];
        $uniqueDepartments[$key] = true;
    }

    // Return count of unique combinations
    return count($uniqueDepartments);
}


// Function to get the key for each department
// This is used to uniquely identify each department
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