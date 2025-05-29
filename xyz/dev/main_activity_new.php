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
$students_assigned = 0;
$rooms = [];
$bench_index = 0;

foreach ($rooms_raw as $room) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hall Ticket</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        /* Screen styling */
body {
    font-family: Arial, sans-serif;
    font-size: 13px;
    padding: 20px;
    margin: 0;
    background: #f9f9f9;
}

h5 {
    margin-top: 25px;
    font-size: 18px;
    color: #333;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
}

/* Table layout */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #000;
    padding: 8px 10px;
    vertical-align: top;
    font-size: 12px;
    min-width: 160px;
    text-align: left;
}

th {
    background: #eaeaea;
}

td .empty {
    color: #888;
    font-style: italic;
}

.page-break {
    page-break-before: always;
}

ul {
    margin: 0;
    padding-left: 20px;
}

ul li {
    margin-bottom: 4px;
}

/* Print Styling */
@media print {
    body {
        padding: 10px;
        background: white;
    }

    .container {
        padding: 0;
        margin: 0;
    }

    table {
        page-break-inside: avoid;
    }

    .page-break {
        page-break-before: always;
    }

    .no-print {
        display: none;
    }

    td, th {
        font-size: 11px;
        padding: 5px;
    }

    h5 {
        font-size: 16px;
        /* margin-top: 20px; */
        color: black;
    }
}

    </style>
</head>
<body>
<div class="container">

<?php
$departmentObj = new Department();
foreach ($rooms as $room_index => $room_grid) {
    $layout = $rooms_layout[$room_index];
    $room_no = $layout['room_no'];
    $room_name = $layout['room_name'];

    echo '<h5 class="fs-5">Room SNO: ' . htmlspecialchars($room_no) . ' - ' . htmlspecialchars($room_name) . '</h5>';
    $row_count = count($room_grid);
    $col_count = count($room_grid[0]);

    echo '<table border="1" cellpadding="5" cellspacing="0">';
    // echo '<tr>';
    // for ($col = 0; $col < $col_count; $col++) {
    //     echo "<th>Col $col</th>";
    // }
    // echo '</tr>';

    echo '<tr>';
    $middle_start = 1;  // Counter for Middle Benches
    for ($col = 0; $col < $col_count; $col++) {
        if ($col === 0) {
            $bench_label = "Left Bench";
        } elseif ($col === $col_count - 1) {
            $bench_label = "Right Bench";
        } else {
            $bench_label = "Middle Bench " . $middle_start++;
        }
        echo "<th>$bench_label</th>";
    }
    echo '</tr>';
    
    

    foreach ($room_grid as $r_index => $row) {
        echo '<tr>';
        foreach ($row as $c_index => $bench) {
            echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";

            foreach (['A', 'B'] as $label) {
                if (!empty($bench[$label])) {
                    $s = $bench[$label];
                    echo "Seat $label: " . htmlspecialchars($s['name']) . ' (' . htmlspecialchars($departmentObj->getDepartmentNameById($s['department_id'])) . ')<br>';
                } else {
                    echo "Seat $label: <span class='empty'></span><br>";
                    // echo "Seat $label: <span class='empty'>Empty</span><br>";

                }
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table><br>';

    // ✅ Check if room has any seated student
    $has_students = false;
    foreach ($room_grid as $row) {
        foreach ($row as $bench) {
            if (!empty($bench['A']) || !empty($bench['B'])) {
                $has_students = true;
                break 2; // Exit both loops
            }
        }
    }
    
    // ✅ Only show page break if there are seated students
    if ($has_students) {
        echo '<div class="page-break"></div>';
    }
    
}

$totalBenches = 0;
foreach ($rooms as $room_index => $room_grid) {
    $totalBenches += count($room_grid) * count($room_grid[0]);
}
// echo "<h4>Total Benches Used: $totalBenches</h4>";

if (!empty($unseated_students)) {
    echo '<h5>Unseated Students</h5>';
    echo "<ul style='list-style-type: disc; padding-left: 20px;'>";
    foreach ($unseated_students as $student) {
        echo '<li>' . htmlspecialchars($student['name']) . ' (' . htmlspecialchars($student['department_id']) . ')</li>';
    }
    echo '</ul>';
}
?>
    <!-- Bootstrap 5.3.3 JS bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</div>
</body>
</html>
