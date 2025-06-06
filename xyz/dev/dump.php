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