<?php 
echo '<pre>';
// print_r(allocateStudentsToRooms($rooms, $totalStudent));
$combinations_list = $combinationStore->findAll();
print_r(getZigzagMergedStudents($combinations_list));
// print_r($totalStudent);
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

<?php   
function getZigzagMergedStudents($data) {
    $result = [];

    foreach ($data as $blockIndex => $block) {
        $departments = [];

        // Skip '_id' and collect students by department
        foreach ($block as $key => $group) {
            if ($key === '_id') continue;

            $deptId = $group['department'];
            foreach ($group['students'] as $student) {
                $departments[$deptId][] = $student;
            }
        }

        // Zigzag merge
        $zigzag = [];
        $max = max(array_map('count', $departments));

        for ($i = 0; $i < $max; $i++) {
            foreach ($departments as $students) {
                if (isset($students[$i])) {
                    $zigzag[] = $students[$i];
                }
            }
        }

        $result[] = [
            'block_id' => $block['_id'] ?? null,
            'zigzag_students' => $zigzag
        ];
    }

    return $result;
}



?>