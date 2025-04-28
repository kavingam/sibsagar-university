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

echo '<div class="container text-center my-5">';
// PHP variables for dynamic content
$academicYear = date('Y');
// $examName = "Entrance Exam"; // Example value
// $examDate = "2025-05-15"; // Example value
// $examTime = "10:00 AM"; // Example value
// assets/Picture-1.png
// Echoing the content dynamically
// echo '<h1 class="display-6">Sibsagar University</h1>';
echo '<img src="assets/Picture-1.png" class="img-fluid" alt="Picture 1">'; 
echo '<p class="fs-5 fw-bold mt-2">Student seat allotment list</p>';
echo '<p class="fs-6">Exam: <strong>' . $examName . '</strong></p>';

// echo '<p class="lead">Academic Year: <strong>' . $academicYear . '</strong></p>';
// echo '<p class="lead">Date: <strong>' . $examDate . '</strong></p>';
// echo '<p class="lead">Time: <strong>' . $examTime . '</strong></p>';
echo '<div class="row">';
echo '<div class="col-6 text-start">';
echo '<p class="fs-6">Academic Year: <strong>' . $academicYear . '</strong></p>';
echo '</div>';
echo '<div class="col-6 text-end">';
echo '<p class="fs-6">Date: <strong>' . $examDate . '</strong></p>';
echo '<p class="fs-6">Time: <strong>' . $examTime . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

?>
<?php
$student_index = 0; // Initialize the student index

foreach ($assignedRooms as $room) {
    $cols = $room['banch_order']; // Number of benches per row (columns)
    $total_seats = $room['students_assigned']; // Total seats in the room
    $rows = ceil($total_seats / ($cols * 2)); // Calculate number of rows based on the columns and 2 seats per bench

    echo "<div class='container mb-5'>";
    echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>{$room['room_no']}</strong> - {$room['room_name']}</h4>";
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

