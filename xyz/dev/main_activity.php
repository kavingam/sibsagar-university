<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === Paths ===
require_once __DIR__ . '/../bashmodel.php';
require_once __DIR__ . '/../seat_allocation/seat_allocation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $examTime24 = htmlspecialchars($data['startTime']);
    $examTime = date('g:i A', strtotime($examTime24));
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $examName = htmlspecialchars($data['selectedExam']);
    $examDate = htmlspecialchars($data['startDate']);
    $saveData = htmlspecialchars($data['save']);
    $tableData = $data['tableData'];

    usort($tableData, fn($a, $b) => $b['totalStudent'] <=> $a['totalStudent']);

    $students = new Student();
    $rooms = new Room();

    $fetchStudents = [];

    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['course'],
            $data['semester']
        );

        $fetchStudents[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'subject' => $data['subject'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    $dept_students = [];

    foreach ($fetchStudents as $entry) {
        if (!isset($entry['students']) || !is_array($entry['students']))
            continue;

        foreach ($entry['students'] as $student) {
            $dept_id = $student['department_id'] ?? null;
            $course = $student['course'] ?? null;
            $semester = $student['semester'] ?? null;

            if ($dept_id !== null && $course !== null && $semester !== null) {
                $key = "{$dept_id}_{$course}_{$semester}";
                $dept_students[$key][] = $student;
            }
        }
    }
    $group_keys = array_keys($dept_students);

    $totalStudents = 0;

    foreach ($fetchStudents as $entry) {
        if (isset($entry['students']) && is_array($entry['students'])) {
            $totalStudents += count($entry['students']);
        }
    }

    // echo 'Total Students: ' . $totalStudents;

    // === Function: Get next department with students (excluding some)
    function getNextDept(array $dept_students, array $exclude = []): ?string
    {
        foreach ($dept_students as $dept => $list) {
            if (!in_array($dept, $exclude) && count($list) > 0) {
                return $dept;
            }
        }
        return null;
    }

    // === Function: Get first N active departments with students
    function getFirstNActiveDepartments(array $dept_students, int $limit = 4): array
    {
        $activeDepartments = [];
        foreach ($dept_students as $dept => $list) {
            if (count($list) > 0) {
                $activeDepartments[] = $dept;
                if (count($activeDepartments) == $limit) {
                    break;
                }
            }
        }
        return $activeDepartments;
    }

    // === Initialize Step-wise Allocation ===
    $step = 1;
    $processingLog = [];

    // Start with up to 4 active departments
    $activeDepartments = getFirstNActiveDepartments($dept_students, 4);

    while (true) {
        $thisStep = [];
        $deptCount = 0;

        for ($i = 0; $i < count($activeDepartments); $i++) {
            $dept = $activeDepartments[$i];

            if (!empty($dept_students[$dept])) {
                $student = array_shift($dept_students[$dept]);  // Remove one student
                // $thisStep[] = "{$dept} ({$student['name']})";
                $thisStep[] = "{$dept} ({$student['name']}) - Roll No: {$student['roll_no']}, Reg No: {$student['reg_no']}, Dept ID: {$student['department_id']}, Semester: {$student['semester']}, Course: {$student['course']}";

                $deptCount++;

                // If department is now empty, find replacement
                if (count($dept_students[$dept]) == 0) {
                    $new_dept = getNextDept($dept_students, $activeDepartments);
                    if ($new_dept) {
                        $activeDepartments[$i] = $new_dept;
                    } else {
                        array_splice($activeDepartments, $i, 1);
                        $i--;
                    }
                }
            }
        }

        if (empty($thisStep))
            break;

        $processingLog[] = [
            'step' => $step,
            'layout' => $deptCount,
            'assigned' => $thisStep
        ];

        $step++;
    }

    // $steps = $processingLog;

    // $flat_students = [];
    // $order_id = 1;  // running ID for each student

    // foreach ($steps as $step) {
    //     // $total_departments = $step['layout'];
    //     foreach ($step['assigned'] as $student_str) {
    //         // Match format: "DEPT (StudentName)"
    //         if (preg_match('/^([A-Z0-9_]+) \(([^)]+)\)$/', $student_str, $matches)) {
    //             $flat_students[] = [
    //                 // 'id' => $order_id++,
    //                 'department' => $matches[1],
    //                 'name' => $matches[2]
    //                 // 'layout' => $total_departments
    //             ];
    //         }
    //     }
    // }
    $steps = $processingLog;

    $flat_students = [];
    $order_id = 1; // optional if needed
    
    foreach ($steps as $step) {
        foreach ($step['assigned'] as $student_str) {
            // Match format: DEPT (Name) - Roll No: ..., Reg No: ..., Dept ID: ..., Semester: ..., Course: ...
            if (preg_match('/^([A-Z0-9_]+) \(([^)]+)\) - Roll No: ([^,]+), Reg No: ([^,]+), Dept ID: ([^,]+), Semester: ([^,]+), Course: ([^,]+)/', $student_str, $matches)) {
                $flat_students[] = [
                    'department'       => $matches[1],
                    'name'             => $matches[2],
                    'roll_no'          => $matches[3],
                    'reg_no'           => $matches[4],
                    'department_id'    => $matches[5],
                    'semester'         => $matches[6],
                    'course'           => $matches[7]
                    // 'id'            => $order_id++ // uncomment if needed
                ];
            }
        }
    }
    
    echo '<pre>';
    // print_r($processingLog);
    // print_r($flat_students);
    // print_r($steps);
    // print_r($dept_students);


    $benches = [];
    $index = 0;
    $student_count = count($flat_students);

    while ($index < $student_count) {
        $bench = ['A' => null, 'B' => null];

        // Assign student A
        $bench['A'] = $flat_students[$index++] ?? null;

        // Find student B from different department
        for ($j = $index; $j < $student_count; $j++) {
            if ($flat_students[$j] !== null &&
                    $bench['A'] !== null &&
                    $flat_students[$j]['department'] !== $bench['A']['department']) {
                $bench['B'] = $flat_students[$j];
                // Remove student B from the list and update counters
                array_splice($flat_students, $j, 1);
                $student_count--;
                break;
            }
        }

        $benches[] = $bench;
    }

    // === Print Processing Log for Debugging ===
    // echo "<pre>";
    // foreach ($processingLog as $log) {
    //     echo "Step {$log['step']} - Assigned: " . implode(', ', $log['assigned']) . "\n";
    // }
    // echo "</pre>";

    print_r('<pre>');
    // print_r($fetchStudents);
    // print_r($dept_students);
    // print_r($processingLog);
    // print_r($flat_students);
    // print_r($benches);
    // print_r($rooms->getAllRooms());
    // print_r(allocateStudentsToRooms($rooms->getAllRooms(),$totalStudents));
    print_r('<pre/>');

    // === Room Layout: [rows × cols] ===
    // $rooms_layout = [
    //     ['rows' => 8, 'cols' => 3],
    //     ['rows' => 8, 'cols' => 3],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 15, 'cols' => 3],
    //     ['rows' => 15, 'cols' => 3],
    //     ['rows' => 15, 'cols' => 2],
    //     ['rows' => 5, 'cols' => 2],
    //     ['rows' => 10, 'cols' => 2],
    //     ['rows' => 5, 'cols' => 2],
    //     ['rows' => 6, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 5],
    //     ['rows' => 20, 'cols' => 3],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2]
    // ];
    $rooms_layout = [];

    $roomData = allocateStudentsToRooms($rooms->getAllRooms(), $totalStudents);

    foreach ($roomData as $room) {
        $cols = (int) $room['banch_order'];
        $rows = (int) floor($room['room_capacity'] / $cols);
        $repeat = (int) ceil($room['students_assigned'] / $room['room_capacity']);

        for ($i = 0; $i < $repeat; $i++) {
            $rooms_layout[] = [
                'room_no' => $room['room_no'],
                'room_name' => $room['room_name'],
                'rows' => $rows,
                'cols' => $cols
            ];
        }
    }
    // $rooms_layout = [
    //     ['rows' => 8, 'cols' => 3],
    //     ['rows' => 8, 'cols' => 3], 
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 15, 'cols' => 3],
    //     ['rows' => 15, 'cols' => 3],
    //     ['rows' => 15, 'cols' => 2],
    //     ['rows' => 5, 'cols' => 2],
    //     ['rows' => 10, 'cols' => 2],
    //     ['rows' => 5, 'cols' => 2],
    //     ['rows' => 6, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 5],
    //     ['rows' => 20, 'cols' => 3],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2],
    //     ['rows' => 7, 'cols' => 2]

    
    // ];

    // print_r($rooms_layout);
}

$rooms = [];
$bench_index = 0;

foreach ($rooms_layout as $layout) {
    $rows = $layout['rows'];
    $cols = $layout['cols'];

    // Initialize grid with empty benches
    $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row < $rows; $row++) {
            if ($bench_index >= count($benches)) {
                break 2;  // No more benches to assign
            }

            $current_bench = $benches[$bench_index];

            // Get departments seated on current bench
            $current_depts = array_filter([
                $current_bench['A']['department'] ?? null,
                $current_bench['B']['department'] ?? null
            ]);

            // Departments seated in the previous row same column
            $prev_depts = [];
            if ($row > 0 && !empty($grid[$row - 1][$col])) {
                $prev = $grid[$row - 1][$col];
                $prev_depts = array_filter([
                    $prev['A']['department'] ?? null,
                    $prev['B']['department'] ?? null
                ]);
            }

            // Check for department repetition vertically
            $conflict = false;
            foreach ($current_depts as $dept) {
                if (in_array($dept, $prev_depts)) {
                    $conflict = true;
                    break;
                }
            }

            if ($conflict) {
                // Leave bench empty to avoid department repetition
                $grid[$row][$col] = ['A' => null, 'B' => null];
            } else {
                // Assign current bench and move to next
                $grid[$row][$col] = $current_bench;
                $bench_index++;
            }
        }
    }

    $rooms[] = $grid;
}

// print_r($rooms);
// === Collect remaining students who couldn't be seated ===
$unseated_students = [];
while ($bench_index < count($benches)) {
    $bench = $benches[$bench_index++];
    foreach (['A', 'B'] as $label) {
        if (!empty($bench[$label])) {
            $unseated_students[] = $bench[$label];
        }
    }
}

// === Output Styling ===
echo '<style>
    table { border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #000; padding: 10px; vertical-align: top; min-width: 200px; text-align: left; }
    .empty { color: #888; font-style: italic; }
    th { background: #eee; }
    h2 { margin-top: 40px; }
</style>';

$departmentObj = new Department();
// === Display Rooms ===
foreach ($rooms as $room_index => $room_grid) {
    $layout = $rooms_layout[$room_index];  // Fetch layout info

    // $room_no = $layout['room_no'];
    // $room_name = $layout['room_name'];
    // echo '<h2>Room ' . htmlspecialchars($room_no) . ' - ' . htmlspecialchars($room_name) . '</h2>';

    $row_count = count($room_grid);
    $col_count = count($room_grid[0]);

    echo '<div class="page-break">';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr>';
    for ($col = 0; $col < $col_count; $col++) {
        echo "<th>Col $col</th>";
    }
    echo '</tr>';

    foreach ($room_grid as $r_index => $row) {
        echo '<tr>';
        foreach ($row as $c_index => $bench) {
            echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";

            foreach (['A', 'B'] as $label) {
                if (!empty($bench[$label])) {
                    $s = $bench[$label];
                    echo "Seat $label: " . htmlspecialchars($s['name']) . ' (' . htmlspecialchars($departmentObj->getDepartmentNameById($s['department_id']))  . ')<br>';
                } else {
                    echo "Seat $label: <span class='empty'>Empty</span><br>";
                }
            }

            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table><br>';
    echo '</div>';
}

// === Display Unseated Students if any ===
if (!empty($unseated_students)) {
    echo '<h2>Unseated Students</h2>';
    echo "<ul style='list-style-type: disc; padding-left: 20px;'>";
    foreach ($unseated_students as $student) {
        echo '<li>' . htmlspecialchars($student['name']) . ' (' . htmlspecialchars($student['department_id']) . ')</li>';
    }
    echo '</ul>';
}

?>

<?php

// === Optional Allocation Function (Used elsewhere?) ===
function allocateStudentsToRooms(array $rooms, int $total_students): array
{
    $assigned = [];
    foreach ($rooms as $room) {
        if ($total_students <= 0)
            break;
        $capacity = $room['seat_capacity'] * 2;
        $assign = min($capacity, $total_students);
        $assigned[] = [
            'room_no' => $room['room_no'],
            'room_name' => $room['room_name'],
            'banch_order' => $room['bench_order'],
            'room_capacity' => $room['seat_capacity'],
            'students_assigned' => $assign
        ];
        $total_students -= $assign;
    }
    return $assigned;
}
?>