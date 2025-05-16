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

    $remaining_departments = $group_keys;
    $step = 1;
    $flat_students = [];

    while (!empty($remaining_departments)) {
        $try_count = 4;

        while ($try_count >= 1) {
            $current_batch = array_slice($remaining_departments, 0, $try_count);

            if (count($current_batch) > 0) {
                $batch_students = [];
                $all_empty = false;

                while (!$all_empty) {
                    $all_empty = true;
                    foreach ($current_batch as $dept) {
                        if (isset($dept_students[$dept]) && !empty($dept_students[$dept])) {
                            $batch_students[] = array_shift($dept_students[$dept]);
                            $all_empty = false;
                        } else {
                            $batch_students[] = null;
                        }
                    }
                }

                $empty_seats = count(array_filter($batch_students, fn($s) => $s === null));
                $remaining_departments = array_slice($remaining_departments, $try_count);

                if ($empty_seats > 0) {
                    $filled_seats = 0;
                    foreach ($remaining_departments as $idx => $dept) {
                        while (isset($dept_students[$dept]) && !empty($dept_students[$dept]) && $filled_seats < $empty_seats) {
                            $null_index = array_search(null, $batch_students, true);
                            if ($null_index === false)
                                break;
                            $batch_students[$null_index] = array_shift($dept_students[$dept]);
                            $filled_seats++;
                        }
                        if (!isset($dept_students[$dept]) || empty($dept_students[$dept])) {
                            unset($remaining_departments[$idx]);
                        }
                        if ($filled_seats >= $empty_seats)
                            break;
                    }
                    $remaining_departments = array_values($remaining_departments);
                }

                $flat_students = array_merge($flat_students, $batch_students);
                break;
            }

            $try_count--;
        }

        $step++;
    }

    $benches = [];
    $index = 0;
    $student_count = count($flat_students);

    while ($index < $student_count) {
        $bench = ['A' => null, 'B' => null];

        if ($flat_students[$index] !== null) {
            $bench['A'] = $flat_students[$index];
        }
        $index++;

        for ($j = $index; $j < $student_count; $j++) {
            $candidate = $flat_students[$j];

            if ($candidate === null) continue;

            if ($bench['A'] === null) {
                $bench['B'] = $candidate;
                array_splice($flat_students, $j, 1);
                $student_count--;
                break;
            }

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
        $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

        for ($col = 0; $col < $cols; $col++) {
            for ($row = 0; $row < $rows; $row++) {
                if ($bench_index >= count($benches)) {
                    break 2;
                }

                $current_bench = $benches[$bench_index];
                $current_depts = array_filter([
                    $current_bench['A']['department_id'] ?? null,
                    $current_bench['B']['department_id'] ?? null,
                ]);

                $prev_depts = [];
                if ($row > 0 && !empty($grid[$row - 1][$col])) {
                    $prev = $grid[$row - 1][$col];
                    $prev_depts = array_filter([
                        $prev['A']['department_id'] ?? null,
                        $prev['B']['department_id'] ?? null,
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
                }
            }
        }

        $rooms[] = $grid;
    }

    // === NEW: Fill only fully empty benches with remaining unused department students ===
    $remaining_students = [];
    foreach ($dept_students as $dept => $students_list) {
        foreach ($students_list as $student) {
            $remaining_students[] = $student;
        }
    }

    foreach ($rooms as &$room) {
        foreach ($room as &$row) {
            foreach ($row as &$bench) {
                if ($bench['A'] === null && $bench['B'] === null && count($remaining_students) > 0) {
                    $bench['A'] = array_shift($remaining_students);
                    if (!empty($remaining_students)) {
                        $bench['B'] = array_shift($remaining_students);
                    }
                }
            }
        }
    }

    $unseated_students = $remaining_students;

    echo '<style>
        table { border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
        td, th { border: 1px solid #000; padding: 10px; min-width: 200px; vertical-align: top; text-align: left; }
        .empty { color: #888; }
        th { background: #eee; }
        h2 { margin-top: 40px; page-break-before: always; }
    </style>';

    foreach ($rooms as $room_index => $room_grid) {
        echo '<div class="page-break">';
        echo '<h2>Room ' . ($room_index + 1) . '</h2>';
        $row_count = count($room_grid);
        $col_count = count($room_grid[0]);

        echo '<table><tr>';
        for ($col = 0; $col < $col_count; $col++) {
            echo "<th>Col $col</th>";
        }
        echo '</tr>';

        foreach ($room_grid as $r_index => $row) {
            echo '<tr>';
            foreach ($row as $c_index => $bench) {
                echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";
                foreach (['A', 'B'] as $seat) {
                    if (!empty($bench[$seat])) {
                        $s = $bench[$seat];
                        echo "Seat $seat: {$s['name']} ({$s['department_name']})<br>";
                        echo "Seat $seat: {$s['roll_no']}<br>";
                    } else {
                        echo "Seat $seat: <span class='empty'>Empty</span><br>";
                    }
                }
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    if (!empty($unseated_students)) {
        echo '<h3>Unseated Students (No space available):</h3><ul>';
        foreach ($unseated_students as $s) {
            echo "<li>{$s['name']} ({$s['department_name']}) - {$s['roll_no']}</li>";
        }
        echo '</ul>';
    }
}
?>
