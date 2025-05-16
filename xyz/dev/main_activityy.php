<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // Sort by student count (descending)
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
    $department_counts = [];

    foreach ($fetchStudents as $entry) {
        if (!isset($entry['students']) || !is_array($entry['students'])) continue;
    
        foreach ($entry['students'] as $student) {
            $dept_id = $student['department_id'] ?? null;
            $course = $student['course'] ?? null;
            $semester = $student['semester'] ?? null;
    
            if ($dept_id !== null && $course !== null && $semester !== null) {
                $key = "{$dept_id}_{$course}_{$semester}";
                $dept_students[$key][] = $student;
                
                if (!isset($department_counts[$dept_id])) {
                    $department_counts[$dept_id] = 0;
                }
                $department_counts[$dept_id]++;
            }
        }
    }

    arsort($department_counts);
    $department_order = array_keys($department_counts);
    $total_departments = count($department_order);

    $flat_students = [];
    $all_empty = false;
    
    while (!$all_empty) {
        $all_empty = true;
        
        foreach ($department_order as $dept_id) {
            foreach ($dept_students as $key => &$students) {
                if (strpos($key, "{$dept_id}_") === 0 && !empty($students)) {
                    $flat_students[] = array_shift($students);
                    $all_empty = false;
                    break;
                }
            }
        }
    }

    $benches = [];
    $index = 0;
    $student_count = count($flat_students);

    while ($index < $student_count) {
        $bench = ['A' => null, 'B' => null];
        $bench['A'] = $flat_students[$index++];
        $current_dept = $bench['A']['department_id'];

        for ($j = $index; $j < $student_count; $j++) {
            $candidate = $flat_students[$j];
            if ($candidate['department_id'] !== $current_dept) {
                $bench['B'] = $candidate;
                array_splice($flat_students, $j, 1);
                $student_count--;
                break;
            }
        }

        $benches[] = $bench;
    }

    $rooms_layout = [
        ['rows' => 8, 'cols' => 3], ['rows' => 8, 'cols' => 3], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2], ['rows' => 15, 'cols' => 3], ['rows' => 15, 'cols' => 3],
        ['rows' => 15, 'cols' => 2], ['rows' => 5, 'cols' => 2], ['rows' => 10, 'cols' => 2],
        ['rows' => 5, 'cols' => 2], ['rows' => 6, 'cols' => 2], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 5], ['rows' => 20, 'cols' => 3], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2], ['rows' => 7, 'cols' => 2]
    ];

    $rooms = [];
    $bench_index = 0;

    foreach ($rooms_layout as $layout) {
        $rows = $layout['rows'];
        $cols = $layout['cols'];
        $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));
        $current_room_departments = [];
        $department_counts_in_room = [];

        for ($col = 0; $col < $cols; $col++) {
            for ($row = 0; $row < $rows; $row++) {
                if ($bench_index >= count($benches)) break 2;

                $current_bench = $benches[$bench_index];
                $bench_depts = array_filter([
                    $current_bench['A']['department_id'] ?? null,
                    $current_bench['B']['department_id'] ?? null
                ]);

                $new_depts = array_diff($bench_depts, $current_room_departments);
                $can_add = count($new_depts) == 0 || count($current_room_departments) + count($new_depts) <= 4;

                if ($can_add) {
                    $conflict = false;
                    if ($row > 0) {
                        $prev_bench = $grid[$row - 1][$col];
                        $prev_depts = array_filter([
                            $prev_bench['A']['department_id'] ?? null,
                            $prev_bench['B']['department_id'] ?? null
                        ]);

                        foreach ($bench_depts as $dept) {
                            if (in_array($dept, $prev_depts)) {
                                $conflict = true;
                                break;
                            }
                        }
                    }

                    if (!$conflict) {
                        $grid[$row][$col] = $current_bench;
                        $bench_index++;

                        foreach ($bench_depts as $dept) {
                            if (!in_array($dept, $current_room_departments)) {
                                $current_room_departments[] = $dept;
                            }
                            if (!isset($department_counts_in_room[$dept])) {
                                $department_counts_in_room[$dept] = 0;
                            }
                            $department_counts_in_room[$dept]++;
                        }
                    }
                }
            }
        }

        $rooms[] = [
            'layout' => $layout,
            'grid' => $grid,
            'departments' => $current_room_departments
        ];
    }

    $unseated_students = [];
    while ($bench_index < count($benches)) {
        $bench = $benches[$bench_index++];
        foreach (['A', 'B'] as $seat) {
            if (!empty($bench[$seat])) {
                $unseated_students[] = $bench[$seat];
            }
        }
    }

    echo "<style>
        table { border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 10px; min-width: 200px; vertical-align: top; text-align: left; }
        .empty { color: #888; }
        th { background: #eee; }
        h2 { margin-top: 40px; }
        .room-info { background: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    </style>";

    foreach ($rooms as $room_index => $room) {
        $room_num = $room_index + 1;
        $dept_list = implode(', ', array_map(function ($dept_id) use ($fetchStudents) {
            foreach ($fetchStudents as $group) {
                if ($group['department'] == $dept_id) {
                    return $group['department_name'] ?? $dept_id;
                }
            }
            return $dept_id;
        }, $room['departments']));

        echo "<div class='room-info'>";
        echo "<h2>Room $room_num</h2>";
        echo "<p><strong>Departments:</strong> $dept_list</p>";
        echo "<p><strong>Layout:</strong> {$room['layout']['rows']} rows × {$room['layout']['cols']} columns</p>";
        echo "</div>";

        $grid = $room['grid'];
        echo "<table><tr>";
        for ($col = 0; $col < $room['layout']['cols']; $col++) {
            echo "<th>Col $col</th>";
        }
        echo "</tr>";

        foreach ($grid as $r_index => $row) {
            echo "<tr>";
            foreach ($row as $c_index => $bench) {
                echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";
                foreach (['A', 'B'] as $seat) {
                    if (!empty($bench[$seat])) {
                        $s = $bench[$seat];
                        $dept_name = $s['department_name'] ?? $s['department_id'];
                        echo "Seat $seat: {$s['name']} ($dept_name)<br>";
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

    if (!empty($unseated_students)) {
        echo "<h3>Unseated Students (No space available):</h3><ul>";
        foreach ($unseated_students as $s) {
            $dept_name = $s['department_name'] ?? $s['department_id'];
            echo "<li>{$s['name']} ($dept_name)</li>";
        }
        echo "</ul>";
    }
}
?>
