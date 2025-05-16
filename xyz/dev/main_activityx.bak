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
        if (!isset($entry['students']) || !is_array($entry['students'])) continue;
    
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

    $flat_students = [];
    $all_empty = false;
    
    while (!$all_empty) {
        $all_empty = true;
    
        foreach ($group_keys as $group_key) {
            if (!empty($dept_students[$group_key])) {
                $flat_students[] = array_shift($dept_students[$group_key]);
                $all_empty = false;
            }
        }
    }
    
    $benches = [];
    $index = 0;
    $student_count = count($flat_students);

    while ($index < $student_count) {
        $bench = ['A' => null, 'B' => null];
        $bench['A'] = $flat_students[$index++];

        for ($j = $index; $j < $student_count; $j++) {
            $candidate = $flat_students[$j];

            // Check if candidate differs in department, course, and semester
            if (
                $candidate['department_id'] !== $bench['A']['department_id'] ||
                $candidate['course'] !== $bench['A']['course'] ||
                $candidate['semester'] !== $bench['A']['semester']
            ) {
                $bench['B'] = $candidate;
                array_splice($flat_students, $j, 1);
                $student_count--;
                break;
            }
        }

        $benches[] = $bench;
    }


    // === Room Layout: [rows × cols] ===
    $rooms_layout = [
        ['rows' => 8, 'cols' => 3],
        ['rows' => 8, 'cols' => 3], 

        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],

        ['rows' => 15, 'cols' => 3],
        ['rows' => 15, 'cols' => 3],

        ['rows' => 15, 'cols' => 2],
        ['rows' => 5, 'cols' => 2],
        ['rows' => 10, 'cols' => 2],
        ['rows' => 5, 'cols' => 2],
        ['rows' => 6, 'cols' => 2],

        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],

        ['rows' => 7, 'cols' => 5],
        ['rows' => 20, 'cols' => 3],

        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2]

    
    ];
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
                    break 2; // break both loops, no more benches
                }
    
                $current_bench = $benches[$bench_index];
    
                // Get department_ids of current bench seats
                $current_depts = array_filter([
                    $current_bench['A']['department_id'] ?? null,
                    $current_bench['B']['department_id'] ?? null,
                ]);
    
                // Get departments in previous row, same column
                $prev_depts = [];
                if ($row > 0 && !empty($grid[$row - 1][$col])) {
                    $prev = $grid[$row - 1][$col];
                    $prev_depts = array_filter([
                        $prev['A']['department_id'] ?? null,
                        $prev['B']['department_id'] ?? null,
                    ]);
                }
    
                // Check for any department clash in column
                $conflict = false;
                foreach ($current_depts as $dept) {
                    if (in_array($dept, $prev_depts)) {
                        $conflict = true;
                        break;
                    }
                }
    
                if ($conflict) {
                    // Leave bench empty to avoid clash
                    $grid[$row][$col] = ['A' => null, 'B' => null];
                } else {
                    $grid[$row][$col] = $current_bench;
                    $bench_index++;
                }
            }
        }
    
        $rooms[] = $grid;
    }
    
    // Collect unseated students from remaining benches
    $unseated_students = [];
    while ($bench_index < count($benches)) {
        $bench = $benches[$bench_index++];
        foreach (['A', 'B'] as $seat) {
            if (!empty($bench[$seat])) {
                $unseated_students[] = $bench[$seat];
            }
        }
    }
    
    // Simple CSS for display
    echo "<style>
        table { border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 10px; min-width: 200px; vertical-align: top; text-align: left; }
        .empty { color: #888; }
        th { background: #eee; }
        h2 { margin-top: 40px; }
    </style>";
    
    // Display each room's seating grid
    foreach ($rooms as $room_index => $room_grid) {
        echo "<h2>Room " . ($room_index + 1) . "</h2>";
        $row_count = count($room_grid);
        $col_count = count($room_grid[0]);
    
        echo "<table><tr>";
        for ($col = 0; $col < $col_count; $col++) {
            echo "<th>Col $col</th>";
        }
        echo "</tr>";
    
        foreach ($room_grid as $r_index => $row) {
            echo "<tr>";
            foreach ($row as $c_index => $bench) {
                echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";
                foreach (['A', 'B'] as $seat) {
                    if (!empty($bench[$seat])) {
                        $s = $bench[$seat];
                        echo "Seat $seat: {$s['name']} ({$s['department_name']})<br>";
                    } else {
                        echo "Seat $seat: <span class='empty'>Empty</span><br>";
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Show unseated students if any
    if (!empty($unseated_students)) {
        echo "<h3>Unseated Students (No space available):</h3><ul>";
        foreach ($unseated_students as $s) {
            echo "<li>{$s['name']} ({$s['department_name']})</li>";
        }
        echo "</ul>";
    }
    
    print_r('<pre>');
    // print_r($fetchStudents );
    // print_r( $flat_students);
    // print_r($benches);
    print_r('</pre>');

}

// === Optional Allocation Function (Used elsewhere?) ===
function allocateStudentsToRooms(array $rooms, int $total_students): array {
    $assigned = [];
    foreach ($rooms as $room) {
        if ($total_students <= 0) break;
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
