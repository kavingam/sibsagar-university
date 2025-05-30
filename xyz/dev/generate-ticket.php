<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ('../bashmodel.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableData = json_decode($_POST['tableData'], true);
    $startTime = $_POST['startTime'];
    $benchSeat = $_POST['benchSeat'];
    $selectedExam = $_POST['selectedExam'];
    $enteredExamName = $_POST['enteredExamName'];
    $startDate = $_POST['startDate'];
    $save = $_POST['save'] ?? 0;

    // usort($tableData, fn($a, $b) => $b['totalStudent'] <=> $a['totalStudent']);
    usort($tableData, function ($a, $b) {
        $aEven = $a['totalStudent'] % 2 === 0;
        $bEven = $b['totalStudent'] % 2 === 0;

        // First group by evenness: even before odd
        if ($aEven !== $bEven) {
            return $bEven <=> $aEven;  // true (1) < false (0) — so even comes first
        }

        // If both even or both odd, sort by totalStudent descending
        return $b['totalStudent'] <=> $a['totalStudent'];
    });

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


    $steps = $processingLog;

    $flat_students = [];
    $order_id = 1;  // optional if needed

    foreach ($steps as $step) {
        foreach ($step['assigned'] as $student_str) {
            // Match format: DEPT (Name) - Roll No: ..., Reg No: ..., Dept ID: ..., Semester: ..., Course: ...
            if (preg_match('/^([A-Z0-9_]+) \(([^)]+)\) - Roll No: ([^,]+), Reg No: ([^,]+), Dept ID: ([^,]+), Semester: ([^,]+), Course: ([^,]+)/', $student_str, $matches)) {
                $flat_students[] = [
                    'department' => $matches[1],
                    'name' => $matches[2],
                    'roll_no' => $matches[3],
                    'reg_no' => $matches[4],
                    'department_id' => $matches[5],
                    'semester' => $matches[6],
                    'course' => $matches[7]
                    // 'id'            => $order_id++ // uncomment if needed
                ];
            }
        }
    }

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
}


$rooms_layout = [];
$rooms_raw = $rooms->getAllRooms(); // Assume this returns array of rooms

usort($rooms_raw, function ($a, $b) {
    return (int)$a['room_no'] - (int)$b['room_no'];
});

// $students_assigned = 0;
// $rooms = [];
// $bench_index = 0;

// foreach ($rooms_raw as $room) {
//     $cols = (int) $room['bench_order'];
//     $capacity = (int) $room['seat_capacity'];
//     $rows = (int) floor($capacity / $cols);
//     $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

//     $hasStudent = false;

//     for ($col = 0; $col < $cols; $col++) {
//         for ($row = 0; $row < $rows; $row++) {
//             if ($bench_index >= count($benches)) break 2;

//             $current_bench = $benches[$bench_index];
//             $current_depts = array_filter([
//                 $current_bench['A']['department'] ?? null,
//                 $current_bench['B']['department'] ?? null
//             ]);

//             $prev_depts = [];
//             if ($row > 0 && !empty($grid[$row - 1][$col])) {
//                 $prev = $grid[$row - 1][$col];
//                 $prev_depts = array_filter([
//                     $prev['A']['department'] ?? null,
//                     $prev['B']['department'] ?? null
//                 ]);
//             }

//             $conflict = false;
//             foreach ($current_depts as $dept) {
//                 if (in_array($dept, $prev_depts)) {
//                     $conflict = true;
//                     break;
//                 }
//             }

//             if ($conflict) {
//                 $grid[$row][$col] = ['A' => null, 'B' => null];
//             } else {
//                 $grid[$row][$col] = $current_bench;
//                 $bench_index++;
//                 $hasStudent = true;
//             }
//         }
//     }

//     if ($hasStudent) {
//         $rooms[] = $grid;
//         $rooms_layout[] = [
//             'room_no' => $room['room_no'],
//             'room_name' => $room['room_name'],
//             'rows' => $rows,
//             'cols' => $cols
//         ];
//     }
// }

// // Collect unseated students
// $unseated_students = [];
// while ($bench_index < count($benches)) {
//     $bench = $benches[$bench_index++];
//     foreach (['A', 'B'] as $label) {
//         if (!empty($bench[$label])) {
//             $unseated_students[] = $bench[$label];
//         }
//     }
// }

$students_assigned = 0;
$rooms = [];
$bench_index = 0;

foreach ($rooms_raw as $room) {
    // Skip room if status is 1
    if ((int)$room['status'] === 1) {
        continue;
    }

    $cols = (int) $room['bench_order'];
    $capacity = (int) $room['seat_capacity'];
    $rows = (int) floor($capacity / $cols);
    $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

    $hasStudent = false;

    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row < $rows; $row++) {
            if ($bench_index >= count($benches)) break 2;

            $current_bench = $benches[$bench_index];
            $current_depts = array_filter([
                $current_bench['A']['department'] ?? null,
                $current_bench['B']['department'] ?? null
            ]);

            $prev_depts = [];
            if ($row > 0 && !empty($grid[$row - 1][$col])) {
                $prev = $grid[$row - 1][$col];
                $prev_depts = array_filter([
                    $prev['A']['department'] ?? null,
                    $prev['B']['department'] ?? null
                ]);
            }

            $conflict = false;
            foreach ($current_depts as $dept) {
                if (in_array($dept, $prev_depts)) {
                    $conflict = true;
                    break;
                }
            }

            if ($conflict) {
                $grid[$row][$col] = ['A' => null, 'B' => null];
            } else {
                $grid[$row][$col] = $current_bench;
                $bench_index++;
                $hasStudent = true;
            }
        }
    }

    if ($hasStudent) {
        $rooms[] = $grid;
        $rooms_layout[] = [
            'room_no' => $room['room_no'],
            'room_name' => $room['room_name'],
            'rows' => $rows,
            'cols' => $cols
        ];
    }
}

// Collect unseated students
$unseated_students = [];
while ($bench_index < count($benches)) {
    $bench = $benches[$bench_index++];
    foreach (['A', 'B'] as $label) {
        if (!empty($bench[$label])) {
            $unseated_students[] = $bench[$label];
        }
    }
}

$assigned_students = [];

foreach ($rooms as $roomIndex => $grid) {
    $room_no = $rooms_layout[$roomIndex]['room_no'] ?? 'UNKNOWN';

    foreach ($grid as $row) {
        foreach ($row as $bench) {
            foreach (['A', 'B'] as $pos) {
                if (!empty($bench[$pos]) && is_array($bench[$pos])) {
                    $student = $bench[$pos];
                    $student['room_no'] = $room_no; // inject room number
                    $assigned_students[] = $student;
                }
            }
        }
    }
}

function groupStudentsByRoom(array $students): array {
    $roomWiseStudents = [];

    foreach ($students as $student) {
        if (!isset($student['room_no']) || empty($student['room_no'])) {
            continue;
        }

        $room = $student['room_no'];
        $roomWiseStudents[$room][] = [
            'name'     => $student['name'] ?? '',
            'roll_no'  => $student['roll_no'] ?? '',
            'reg_no'   => $student['reg_no'] ?? '',
            'subject'  => $student['subject'] ?? 'EDU',
        ];
    }

    return $roomWiseStudents;
}

$roomWiseStudents = groupStudentsByRoom($assigned_students);

?>


<?php
// Assuming you have $rooms, $rooms_layout, and $unseated_students already defined
$departmentObj = new Department();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Room-wise Assigned Students</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .ticket-card {
            border: 1px solid #000;
            padding: 12px;
            height: 180px;
            font-size: 14px;
            text-align: center;
        }
        .room-title {
            text-align: center;
            font-weight: bold;
            margin: 30px 0 15px;
            font-size: 18px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        @media print {
            /* Container for tickets per room: force 3 columns */
            .room-section > .row {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 10px;
            }

            /* Ensure tickets don't break across pages */
            .ticket-card {
                page-break-inside: avoid;
                break-inside: avoid;
                /* Adjust height if needed */
                height: auto !important;
                padding: 10px !important;
                font-size: 12px !important;
            }

            /* Page break after each room */
            .page-break {
                page-break-after: always;
                break-after: page;
            }

            /* Remove margins/padding that can cause blank pages */
            body, .container {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Remove Bootstrap print overrides forcing block stacking */
            .col-md-4, .col-sm-6, .col-12 {
                float: none !important;
                width: auto !important;
                max-width: none !important;
            }
        }

    </style>
</head>
<body>
<div class="container">

<?php
// Sample grouped array: $roomWiseStudents = ['UGC1' => [...], 'UGC2' => [...]];
foreach ($roomWiseStudents as $room => $students): ?>
    <div class="room-section">
        <div class="room-title">ROOM: <?= htmlspecialchars($room) ?></div>
        <div class="row">
            <?php foreach ($students as $i => $student): ?>
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <div class="ticket-card">
                        <strong>4<sup>th</sup> SEM FINAL EXAM-2024</strong><br>
                        <!-- <small>(UNDER AUTONOMOUS)</small><br> -->
                        SL. NO: <?= $i + 1 ?> ROOM: <?= htmlspecialchars($room) ?><br><br>
                        <strong>ROLL NO: <?= htmlspecialchars($student['roll_no']) ?></strong><br><br>
                        REG. NO: <?= htmlspecialchars($student['reg_no']) ?><br>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="page-break"></div>
<?php endforeach; ?>

</div>
</body>
</html>
<!-- SUB: <?php // strtoupper($student['subject'] ?? 'EDU') ?> -->