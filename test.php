<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'xyz/bashmodel.php';

$studentObj = new Student();
$departmentObj = new Department();
$roomObj = new Room();
// #############################################################
// DEBUGING
print_r('<pre>');
// print_r($studentObj->getAllStudents());
// print_r($roomObj->getAllRooms());
// print_r($departmentObj->getAllDepartments());
print_r('</pre>');

// #############################################################
?>
<?php
// === Define department-wise students ===
$dept_students = [];

for ($i = 1; $i <= 14; $i++)
	$dept_students['ASS'][] = ['id' => $i, 'name' => "ASS_Student{$i}", 'department' => 'ASS'];
for ($i = 1; $i <= 13; $i++)
	$dept_students['ENG'][] = ['id' => 100 + $i, 'name' => "ENG_Student{$i}", 'department' => 'ENG'];
for ($i = 1; $i <= 23; $i++)
	$dept_students['HIS'][] = ['id' => 200 + $i, 'name' => "HIS_Student{$i}", 'department' => 'HIS'];
// for ($i = 1; $i <= 10; $i++)
// 	$dept_students['POLY'][] = ['id' => 300 + $i, 'name' => "POLY_Student{$i}", 'department' => 'POLY'];
// for ($i = 1; $i <= 36; $i++)
// 	$dept_students['MCA'][] = ['id' => 400 + $i, 'name' => "MCA_Student{$i}", 'department' => 'MCA'];
// for ($i = 1; $i <= 24; $i++)
// 	$dept_students['BOT'][] = ['id' => 500 + $i, 'name' => "BOT_Student{$i}", 'department' => 'BOT'];
// for ($i = 1; $i <= 46; $i++)
// 	$dept_students['CHEM'][] = ['id' => 600 + $i, 'name' => "CHEM_Student{$i}", 'department' => 'CHEM'];
// for ($i = 1; $i <= 43; $i++)
// 	$dept_students['MATH'][] = ['id' => 700 + $i, 'name' => "MATH_Student{$i}", 'department' => 'MATH'];
// for ($i = 1; $i <= 23; $i++)
// 	$dept_students['STS'][] = ['id' => 800 + $i, 'name' => "STS_Student{$i}", 'department' => 'STS'];
// for ($i = 1; $i <= 62; $i++)
// 	$dept_students['CS'][] = ['id' => 900 + $i, 'name' => "CS_Student{$i}", 'department' => 'CS'];
// for ($i = 1; $i <= 45; $i++)
// 	$dept_students['LLM'][] = ['id' => 1100 + $i, 'name' => "LLM_Student{$i}", 'department' => 'LLM'];
// for ($i = 1; $i <= 26; $i++)
// 	$dept_students['MBA'][] = ['id' => 1200 + $i, 'name' => "MBA_Student{$i}", 'department' => 'MBA'];
// for ($i = 1; $i <= 45; $i++)
// 	$dept_students['PHY'][] = ['id' => 1300 + $i, 'name' => "PHY_Student{$i}", 'department' => 'PHY'];
// for ($i = 1; $i <= 36; $i++)
// 	$dept_students['LF'][] = ['id' => 1400 + $i, 'name' => "LF_Student{$i}", 'department' => 'LF'];


// echo '<pre>';
// print_r($dept_students);
// === Helper to calculate total students
$total_students = 0;
foreach ($dept_students as $students) {
	$total_students += count($students);
}
echo "<b>Total Students:</b> $total_students<br><br>";

// === Function: get next department with students (excluding some)
function getNextDept($dept_students, $exclude = [])
{
	foreach ($dept_students as $dept => $list) {
		if (!in_array($dept, $exclude) && count($list) > 0) {
			return $dept;
		}
	}
	return null;
}

// === Initialize first 4 active departments
$activeDepartments = [];
foreach ($dept_students as $dept => $list) {
	if (count($list) > 0) {
		$activeDepartments[] = $dept;
		if (count($activeDepartments) == 4)
			break;
	}
}

// === Start allocation round-robin
// $step = 1;
// $processingLog = [];

// while (true) {
//     $thisStep = [];

//     for ($i = 0; $i < count($activeDepartments); $i++) {
//         $dept = $activeDepartments[$i];

//         if (!empty($dept_students[$dept])) {
//             $student = array_shift($dept_students[$dept]); // Remove one student
//             $thisStep[] = "{$dept} ({$student['name']})";

//             // If department is now empty, find replacement
//             if (count($dept_students[$dept]) == 0) {
//                 $new_dept = getNextDept($dept_students, $activeDepartments);
//                 if ($new_dept) {
//                     $activeDepartments[$i] = $new_dept;
//                 } else {
//                     // Remove from active list
//                     array_splice($activeDepartments, $i, 1);
//                     $i--; // Adjust index after removal
//                 }
//             }
//         }
//     }

//     if (empty($thisStep)) break; // Done

//     $processingLog[] = [
//         'step' => $step,
//         'assigned' => $thisStep
//     ];

//     $step++;
// }

// === Start allocation round-robin
$step = 1;
$processingLog = [];

while (true) {
	$thisStep = [];
	$deptCount = 0;

	for ($i = 0; $i < count($activeDepartments); $i++) {
		$dept = $activeDepartments[$i];

		if (!empty($dept_students[$dept])) {
			$student = array_shift($dept_students[$dept]);  // Remove one student
			$thisStep[] = "{$dept} ({$student['name']})";
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
		break;  // Done

	$processingLog[] = [
		'step' => $step,
		'layout' => $deptCount,
		'assigned' => $thisStep
	];

	$step++;
}

// $steps = $processingLog;

// // 1. Flatten all students into a single list
// $flat_students = [];
// foreach ($steps as $step) {
//     foreach ($step['assigned'] as $student_str) {
//         // Parse department and name
//         if (preg_match('/^([A-Z]+) \(([^)]+)\)$/', $student_str, $matches)) {
//             $flat_students[] = [
//                 'department' => $matches[1],
//                 'name' => $matches[2]
//             ];
//         }
//     }
// }

echo '<pre>';
// print_r($processingLog);

$steps = $processingLog;

$flat_students = [];
$order_id = 1;  // running ID for each student

foreach ($steps as $step) {
	$total_departments = $step['layout'];
	foreach ($step['assigned'] as $student_str) {
		// Match format: "ASS (ASS_Student1)"
		if (preg_match('/^([A-Z]+) \(([^)]+)\)$/', $student_str, $matches)) {
			$flat_students[] = [
				'id' => $order_id++,
				'department' => $matches[1],
				'name' => $matches[2],
				'layout' => $total_departments
			];
		}
	}
}

echo '<pre>';
// print_r($flat_students);
// 2. Assign benches: each with A and B, avoiding same department in both if possible
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
	['rows' => 7, 'cols' => 2]
];
$rooms = [];
$bench_index = 0;

echo '<pre>';
print_r($rooms_layout);

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

// === Remaining students after all rooms are full ===
$unseated_students = [];
while ($bench_index < count($benches)) {
	$bench = $benches[$bench_index++];
	foreach (['A', 'B'] as $label) {
		if (!empty($bench[$label])) {
			$unseated_students[] = $bench[$label];
		}
	}
}

echo '<pre>';
// print_r($benches);
// print_r($rooms);
echo '</pre>';
// === Styling for HTML output ===
echo '<style>
    table { border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #000; padding: 10px; vertical-align: top; min-width: 200px; text-align: left; }
    .empty { color: #888; }
    th { background: #eee; }
    h2 { margin-top: 40px; }
</style>';

// === Display Room Grids ===
foreach ($rooms as $room_index => $room_grid) {
	echo '<h2>Room ' . ($room_index + 1) . '</h2>';
	$row_count = count($room_grid);
	$col_count = count($room_grid[0]);

	echo '<table>';
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
					echo "Seat $label: {$s['name']} ({$s['department']})<br>";
				} else {
					echo "Seat $label: <span class='empty'>Empty</span><br>";
				}
			}

			echo '</td>';
		}
		echo '</tr>';
	}

	echo '</table>';
}

// === Show unseated students (if any) ===
if (!empty($unseated_students)) {
	echo '<h2>Unseated Students</h2>';
	echo "<ul style='list-style-type: disc; padding-left: 20px;'>";
	foreach ($unseated_students as $student) {
		echo '<li>' . htmlspecialchars($student['name']) . ' (' . htmlspecialchars($student['department']) . ')</li>';
	}
	echo '</ul>';
}
?>