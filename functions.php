<?php 
function findBestFitRoom($room_capacities, $required_capacity) {
    sort($room_capacities);
    $best_fit = null;

    foreach ($room_capacities as $capacity) {
        if ($capacity >= $required_capacity) {
            $best_fit = $capacity;
            break;
        }
    }

    return $best_fit;
}

?>
<?php
function knnAllocateRooms($room_capacities, $total_students) {
    // Sort room capacities in ascending order for KNN search
    sort($room_capacities);

    $allocated_rooms = [];
    $remaining_students = $total_students;
    $total_seated = 0;

    while ($remaining_students > 0 && !empty($room_capacities)) {
        // Find the closest room capacity using KNN logic
        $nearest_room = null;
        $min_difference = PHP_INT_MAX;
        $room_index = -1;

        foreach ($room_capacities as $index => $capacity) {
            $difference = abs($capacity - $remaining_students);

            if ($difference < $min_difference) {
                $min_difference = $difference;
                $nearest_room = $capacity;
                $room_index = $index;
            }
        }

        // If no suitable room is found, break
        if ($nearest_room === null) {
            break;
        }

        // Assign students to the best-matching room
        $students_assigned = min($nearest_room, $remaining_students);
        $allocated_rooms[] = [
            'room_capacity' => $nearest_room,
            'students_assigned' => $students_assigned
        ];

        // Reduce remaining students
        $remaining_students -= $students_assigned;
        $total_seated += $students_assigned;

        // Remove the used room to avoid duplicate allocation
        unset($room_capacities[$room_index]);
        $room_capacities = array_values($room_capacities); // Re-index array
    }

    return [$allocated_rooms, $total_seated, $remaining_students];
}
/*
// Example Data
$room_seat_capacity = [20, 30, 32, 45]; // Given room capacities (fixed)
$total_students = 150; // Total students to allocate

// Allocate rooms using KNN logic
list($rooms, $total_seated, $remaining_students) = knnAllocateRooms($room_seat_capacity, $total_students);

// Display results
echo "<h3>Room Allocation using KNN for $total_students Students</h3>\n";
foreach ($rooms as $index => $room) {
    echo "Room " . ($index + 1) . ": Capacity " . $room['room_capacity'] . " | Students Assigned: " . $room['students_assigned'] . "<br>\n";
}

// Display total students seated
echo "<h3>Total Students Seated: $total_seated</h3>\n";

// If students are still left, show warning
if ($remaining_students > 0) {
    echo "<h3 style='color: red;'>Warning: $remaining_students students could not be seated! No more available rooms.</h3>\n";
}
    */
?>

<?php
function findNearestRoom($room_capacities, $required_capacity) {
    // Sort capacities in ascending order
    sort($room_capacities);

    // Initialize nearest values
    $nearest = null;
    $min_difference = PHP_INT_MAX;

    // Loop through available capacities
    foreach ($room_capacities as $capacity) {
        $difference = abs($capacity - $required_capacity);
        
        if ($difference < $min_difference) {
            $min_difference = $difference;
            $nearest = $capacity;
        }
    }

    return $nearest;
}

// Room seat capacities
// $room_seat_capacity = [20, 30, 32, 45];

// Required room capacity
// $required_capacity = 28;

// Find the best match using KNN approach
// $best_room = findNearestRoom($room_seat_capacity, $required_capacity);

// echo "The best room capacity for $required_capacity students is: $best_room";
?>
