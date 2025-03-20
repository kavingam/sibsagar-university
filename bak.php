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






        
        





// Ensure $groupedStudents is properly structured
$groupedStudents = reset($groupedStudents); // Extracts the inner associative array
$groupedStudents = array_values($groupedStudents); // Reindex numerically

$total_students = count($groupedStudents);
$student_count = 0;
$lastBenchCourse = null;
$lastBenchSemester = null;

