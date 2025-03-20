function allocateFlexibleRooms($room_capacities, $bench_seat, $total_students)
{
    // Sort rooms by seat capacity in ascending order
    usort($room_capacities, function ($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    $allocated_rooms = [];
    $remaining_students = $total_students;
    $total_seated = 0;
    $total_benches_used = 0;
    $bench_allocation = [];  // Track which students are seated on which bench

    while ($remaining_students > 0 && !empty($room_capacities)) {
        // Find the closest room capacity using KNN logic
        $nearest_room = null;
        $min_difference = PHP_INT_MAX;
        $room_index = -1;

        foreach ($room_capacities as $index => $room) {
            $actual_capacity = $room['seat_capacity'] * $bench_seat;
            $difference = abs($actual_capacity - $remaining_students);

            if ($difference < $min_difference) {
                $min_difference = $difference;
                $nearest_room = $room;
                $room_index = $index;
            }
        }

        // If no suitable room is found, break
        if ($nearest_room === null) {
            break;
        }

        // Calculate actual seating capacity
        $actual_capacity = $nearest_room['seat_capacity'] * $bench_seat;
        $students_assigned = min($actual_capacity, $remaining_students);
        $benches_used = ceil($students_assigned / $bench_seat);

        // Track assigned students
        $bench_counter = 1;
        for ($i = 0; $i < $students_assigned; $i++) {
            $bench_key = 'Bench ' . $bench_counter;

            if (!isset($bench_allocation[$bench_key])) {
                $bench_allocation[$bench_key] = [];
            }

            $bench_allocation[$bench_key][] = 'Student ' . ($total_seated + 1);

            // Prevent conflicts by moving to the next bench
            $bench_counter++;
            if ($bench_counter > $benches_used) {
                $bench_counter = 1;
            }

            $total_seated++;
        }

        // Store allocated room details
        $allocated_rooms[] = [
            'room_name' => $nearest_room['room_name'],
            'bench_order' => $nearest_room['bench_order'],
            'room_capacity' => $nearest_room['seat_capacity'],
            'bench_seat' => $bench_seat,
            'actual_seating_capacity' => $actual_capacity,
            'students_assigned' => $students_assigned,
            'benches_used' => $benches_used,
            'bench_allocation' => $bench_allocation
        ];

        // Reduce remaining students
        $remaining_students -= $students_assigned;

        // Remove used room
        unset($room_capacities[$room_index]);
        $room_capacities = array_values($room_capacities);
    }

    return [$allocated_rooms, $total_seated, $remaining_students];
}
?>





$room_names = array_column($rooms, 'room_name'); // Extract all room names
$room_counts = array_count_values($room_names); // Count occurrences of each room name

$rooms_to_remove = $room_names;

$filtered_rooms = array_filter($allRooms, function ($room) use ($rooms_to_remove) {
    return isset($room['room_name']) && !in_array($room['room_name'], $rooms_to_remove);
});

// ✅ Now `$filtered_rooms` contains all rooms except NS 8 C and NS 8 D
// print_r(array_values($filtered_rooms)); // Re-index array
        
        


list($flexible_rooms, $flexible_total_seated, $flexible_remaining_students) =  knnAllocateRooms(array_values($filtered_rooms), 1, $total_groupedStudents);
// print_r($groupedStudents);

// Ensure $groupedStudents is properly structured
$groupedStudents = reset($groupedStudents); // Extracts the inner associative array
$groupedStudents = array_values($groupedStudents); // Reindex numerically

$total_students = count($groupedStudents);
$student_count = 0;
$lastBenchCourse = null;
$lastBenchSemester = null;

echo '<hr/>';
echo "<div class='container p-2'>";
echo "<div class='row'>";

// Iterate over flexible rooms
foreach ($flexible_rooms as $fxroom) {
    $benches_per_row = ceil($fxroom['benches_used'] / $fxroom['bench_order']);
    echo "<div class='col-md-6 col-lg-4'>"; // Responsive grid layout
    echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room " . $fxroom['room_name'] . '</h5>';
    echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>";
    echo '<thead>';
    echo "<tr style='border: 1px solid black;'>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Bench No.</th>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    for ($bench = 1; $bench <= $fxroom['benches_used']; $bench++) {
        
        echo "<tr style='border: 1px solid black;'>";

        // Bench Number
        echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench $bench</strong></td>";

        if ($bench % 2 == 1) {  // Odd benches: Assign left, leave right empty
            // Left Seat
            if ($student_count < $total_students) {
                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($groupedStudents[$student_count]['roll_no']) . '</td>';
                $student_count++;
            } else {
                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
            }

            // Right Seat (empty)
            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
        } else {  // Even benches: Leave left empty, assign right
            // Left Seat (empty)
            echo "<td style='border: 1px solid black; padding: 5px;'></td>";

            // Right Seat
            if ($student_count < $total_students) {
                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($groupedStudents[$student_count]['roll_no']) . '</td>';
                $student_count++;
            } else {
                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
            }
        }

        echo '</tr>';
    }
    // }

    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // Close col div
}

echo '</div>'; // Close row div
echo '</div>'; // Close container