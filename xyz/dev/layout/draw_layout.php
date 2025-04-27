<?php 
echo '<pre>';

// print_r(allocateStudentsToRooms($rooms, $totalStudent));

// $seatAllocationStore->findAll();

// print_r($seatAllocationStore->findAll());

$data = $seatAllocationStore->findAll();
$students = [];

foreach ($data as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

// print_r($students);
$assignedRooms = allocateStudentsToRooms($rooms, count($students));
// print_r($assignedRooms);

// print_r();
echo '</pre>';

?>

<?php
// Make sure to include Bootstrap CSS in your HTML head or via CDN:
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';

// $student_index = 0;

// foreach ($assignedRooms as $room) {
//     $cols = $room['banch_order'];
//     $total_seats = $room['students_assigned'];
//     $rows = ceil($total_seats / ($cols * 2));

//     echo "<div class='container mb-5'>";
//     echo "<h4 class='mb-3 text-primary'>Room No: <strong>{$room['room_no']}</strong> - {$room['room_name']}</h4>";
//     echo "<div class='table-responsive'>";
//     echo "<table class='table table-bordered text-center align-middle'>";

//     for ($i = 0; $i < $rows; $i++) {
//         echo "<tr>";
//         for ($j = 0; $j < $cols; $j++) {
//             echo "<td style='min-width:180px;' class='text-nowrap text-center'>";
//             // Print two student roll numbers in one cell using only one variable:
//             echo (isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "");
//             echo " | ";
//             echo (isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "");
//             echo "</td>";
//         }
//         echo "</tr>";
//     }

//     echo "</table>";
//     echo "</div>";
//     echo "</div>";
// }
?>
<?php
$student_index = 0; // Initialize the student index

foreach ($assignedRooms as $room) {
    $cols = $room['banch_order']; // Number of benches per row (columns)
    $total_seats = $room['students_assigned']; // Total seats in the room
    $rows = ceil($total_seats / ($cols * 2)); // Calculate number of rows based on the columns and 2 seats per bench

    echo "<div class='container mb-5'>";
    echo "<h4 class='mb-3 text-primary'>Room No: <strong>{$room['room_no']}</strong> - {$room['room_name']}</h4>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered text-center align-middle'>";
    
    // For each row
    for ($i = 0; $i < $rows; $i++) {
        echo "<tr>";

        for ($i = 0; $i < $rows; $i++) {
            echo "<tr>";
        
            for ($j = 0; $j < $cols; $j++) {
                echo "<td style='min-width:180px;' class='text-nowrap text-center'>";
        
                if ($i % 2 == 1) {
                    // Reverse seat order within the bench on odd rows
                    $seat2 = isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "";
                    $seat1 = isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "";
                } else {
                    // Normal seat order within the bench on even rows
                    $seat1 = isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "";
                    $seat2 = isset($students[$student_index]) ? $students[$student_index++]['roll_no'] : "";
                }
        
                // echo "<div class='fw-bold text-success'>{$seat1}</div> | <div class='fw-bold text-info'>{$seat2}</div>";
                echo "<span class='fw-boldx text-successx me-1'>{$seat1}</span> | <span class='fw-boldx text-infox ms-1'>{$seat2}</span>";

                echo "</td>";
            }
        
            echo "</tr>";
        }
        

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
    echo "</div>";
}

