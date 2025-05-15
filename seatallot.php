<?php
// === Configuration ===
$departments = ['ASS', 'ENG', 'HIS', 'POLY', 'MCA'];
$dept_students = [];
foreach ($departments as $dept) {
    $dept_students[$dept] = [];
}

// Add students to departments
for ($i = 1; $i <= 4; $i++) $dept_students['ASS'][] = ['id' => $i, 'name' => "ASS_Student{$i}", 'department' => 'ASS'];
for ($i = 1; $i <= 10; $i++) $dept_students['ENG'][] = ['id' => 100 + $i, 'name' => "ENG_Student{$i}", 'department' => 'ENG'];
for ($i = 1; $i <= 20; $i++) $dept_students['HIS'][] = ['id' => 200 + $i, 'name' => "HIS_Student{$i}", 'department' => 'HIS'];
for ($i = 1; $i <= 20; $i++) $dept_students['POLY'][] = ['id' => 300 + $i, 'name' => "POLY_Student{$i}", 'department' => 'POLY'];
for ($i = 1; $i <= 10; $i++) $dept_students['MCA'][] = ['id' => 400 + $i, 'name' => "MCA_Student{$i}", 'department' => 'MCA'];

// === Round-robin flattening ===
$flat_students = [];
$all_empty = false;
while (!$all_empty) {
    $all_empty = true;
    foreach ($departments as $dept) {
        if (!empty($dept_students[$dept])) {
            $flat_students[] = array_shift($dept_students[$dept]);
            $all_empty = false;
        }
    }
}

// === Bench Assignment (2 students per bench: A & B) ===
$benches = [];
$index = 0;
$student_count = count($flat_students);

while ($index < $student_count) {
    $bench = ['A' => null, 'B' => null];
    $bench['A'] = $flat_students[$index++] ?? null;

    for ($j = $index; $j < $student_count; $j++) {
        if ($flat_students[$j]['department'] !== $bench['A']['department']) {
            $bench['B'] = $flat_students[$j];
            array_splice($flat_students, $j, 1);
            $student_count--;
            break;
        }
    }

    $benches[] = $bench;
}

// === Room Layout: [rows × cols] ===
$rooms_layout = [
    ['rows' => 10, 'cols' => 3],
    ['rows' => 4, 'cols' => 3], 
    ['rows' => 5, 'cols' => 2], 
];

// === Assign benches into room grids (avoid same dept in column-wise A/B seats)
$rooms = [];
$bench_index = 0;

foreach ($rooms_layout as $layout) {
    $rows = $layout['rows'];
    $cols = $layout['cols'];
    $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row < $rows; $row++) {
            if ($bench_index >= count($benches)) {
                break;
            }

            $current_bench = $benches[$bench_index];

            // Get departments of current bench
            $current_depts = array_filter([
                $current_bench['A']['department'] ?? null,
                $current_bench['B']['department'] ?? null
            ]);

            // Check previous row in the same column
            $prev_depts = [];
            if ($row > 0 && !empty($grid[$row - 1][$col])) {
                $prev = $grid[$row - 1][$col];
                $prev_depts = array_filter([
                    $prev['A']['department'] ?? null,
                    $prev['B']['department'] ?? null
                ]);
            }

            // Check for department clash
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
                // Assign bench
                $grid[$row][$col] = $current_bench;
                $bench_index++;
            }
        }
    }

    $rooms[] = $grid;
}

// === Remaining students after all rooms are full
$unseated_students = [];
while ($bench_index < count($benches)) {
    $bench = $benches[$bench_index++];
    foreach (['A', 'B'] as $label) {
        if (!empty($bench[$label])) {
            $unseated_students[] = $bench[$label];
        }
    }
}

// === Styling for HTML output ===
echo "<style>
    table { border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #000; padding: 10px; vertical-align: top; min-width: 200px; text-align: left; }
    .empty { color: #888; }
    th { background: #eee; }
    h2 { margin-top: 40px; }
</style>";

// === Display Room Grids ===
foreach ($rooms as $room_index => $room_grid) {
    echo "<h2>Room " . ($room_index + 1) . "</h2>";
    $row_count = count($room_grid);
    $col_count = count($room_grid[0]);

    echo "<table>";
    // echo "<tr><th>Row \\ Col</th>";
    for ($col = 0; $col < $col_count; $col++) {
        echo "<th>Col $col</th>";
    }
    echo "</tr>";

    foreach ($room_grid as $r_index => $row) {
        echo "<tr>";
        // echo "<th>Row $r_index</th>";
        foreach ($row as $c_index => $bench) {
            echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";

            foreach (['A', 'B'] as $label) {
                if (!empty($bench[$label])) {
                    $s = $bench[$label];
                    echo "Seat $label: {$s['name']} ({$s['department']})<br>";
                } else {
                    echo "Seat $label: <span class='empty'>Empty</span><br>";
                }
            }

            echo "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}

// === Show unseated students (if any) ===
if (!empty($unseated_students)) {
    echo "<h3>Unseated Students (No space available):</h3><ul>";
    foreach ($unseated_students as $s) {
        echo "<li>{$s['name']} ({$s['department']})</li>";
    }
    echo "</ul>";
}
?>
