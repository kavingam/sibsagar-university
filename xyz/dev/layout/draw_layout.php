<?php 
echo '<pre>';
// print_r(allocateStudentsToRooms($rooms, $totalStudent));
// Output
// echo "<h3>Room Allocation</h3>";
// echo "<table border='1' cellpadding='5'><tr><th>Room No</th><th>Room Name</th><th>Students Assigned</th></tr>";
// foreach ($assigned_rooms as $r) {
//     echo "<tr>
//         <td>{$r['room_no']}</td>
//         <td>{$r['room_name']}</td>
//         <td>{$r['students_assigned']}</td>
//     </tr>";
// }
// echo "</table>";
// print_r($combinationStore->findAll());
// print_r($remainderStore->findAll());

$combinations_list = $combinationStore->findAll();

// foreach ($combinations_list as $level1) {
    // foreach ($level1 as $record) {

    //     // Ensure it's an array to avoid warning
    //     if (is_array($record)) {
    //         echo "<strong>Department:</strong> " . (isset($record['department']) ? $record['department'] : 'N/A') . "<br>";
    //         echo "<strong>Semester:</strong> " . (isset($record['semester']) ? $record['semester'] : 'N/A') . "<br>";
    //         echo "<strong>Course:</strong> " . (isset($record['course']) ? $record['course'] : 'N/A') . "<br>";
    //         echo "<strong>Total Students:</strong> " . (isset($record['totalStudent']) ? $record['totalStudent'] : 'N/A') . "<br><br>";

    //         echo "<strong>Students:</strong><br>";

    //         if (isset($record['students']) && is_array($record['students'])) {
    //             echo "<ul>";
    //             foreach ($record['students'] as $student) {
    //                 if (is_array($student)) {
    //                     echo "<li>";
    //                     echo "Roll No: " . (isset($student['roll_no']) ? $student['roll_no'] : 'N/A') . " | ";
    //                     echo "Name: " . (isset($student['name']) ? $student['name'] : 'N/A');
    //                     echo "</li>";
    //                 }
    //             }
    //             echo "</ul>";
    //         } else {
    //             echo "No students found.<br>";
    //         }

    //         echo "<hr>";
    //     }
    // }
// }

$all_students = [];
// Step 1: Combine all students into one array
for ($i = 0; $i < count($combinations_list); $i++) {
    // Access each department
    $level1 = $combinations_list[$i];

    for ($j = 0; $j < count($level1); $j++) {
        // Access each record inside the department
        $record = $level1[$j];

        // Check if 'students' key exists and merge into the all_students array
        if (isset($record['students'])) {
            $all_students = array_merge($all_students, $record['students']);
        }
    }
}

// print_r($all_students);
// $totalStudents = count($all_students);
// print_r($totalStudents);
?>
<?php

function allocateStudentsToRooms(array $rooms, int $total_students): array {
    $assigned_rooms = [];

    foreach ($rooms as $room) {
        if ($total_students <= 0) break;

        $room_capacity = $room['seat_capacity'] * 2; // 2 seats per bench
        $students_in_room = min($room_capacity, $total_students);

        $assigned_rooms[] = [
            "room_no" => $room['room_no'],
            "room_name" => $room['room_name'],
            "students_assigned" => $students_in_room
        ];

        $total_students -= $students_in_room;
    }

    return $assigned_rooms;
}

?>