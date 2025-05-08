<?php
// Fetch and flatten student list
$data = $seatAllocationStore->findAll();
$students = [];

foreach ($data as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

// echo '<pre>';
// print_r($students);
// print_r($saveData);
// echo '</pre>';
// Allocate students to rooms
$assignedRooms = allocateStudentsToRooms($rooms, count($students));

// Academic details
$academicYear = date('Y');

// echo '<link rel="stylesheet" href="a4.css">';
?>

<!-- Header Info -->
<div class="container text-center my-5">
    <img src="assets/Picture-1.png" class="img-fluid" alt="Picture 1">
    <p class="fs-5 fw-bold mt-2">Student seat allotment list</p>
    <p class="fs-6">Exam: <strong><?= htmlspecialchars($examName ?? '') ?></strong></p>
    <div class="row">
        <div class="col-6 text-start">
            <p class="fs-6">Academic Year: <strong><?= $academicYear ?></strong></p>
        </div>
        <div class="col-6 text-end">
            <p class="fs-6">Date: <strong><?= htmlspecialchars($examDate ?? '') ?></strong></p>
            <p class="fs-6">Time: <strong><?= htmlspecialchars($examTime ?? '') ?></strong></p>
        </div>
    </div>
</div>

<?php
$student_index = 0;

foreach ($assignedRooms as $room) {
    $total_seats = $room['students_assigned'];
    $cols = $room['banch_order']; // Number of columns
    $rows = ceil($total_seats / ($cols * 2)); // 2 seats per column

    echo "<div class='container mb-5'>";
    echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>" . htmlspecialchars($room['room_no']) . "</strong> - " . htmlspecialchars($room['room_name']) . "</h4>";

    // for ($i = 0; $i < $rows; $i++) {
    //     echo "<div class='row gx-4 gy-3'>";

    //     for ($j = 0; $j < $cols; $j++) {
    //         $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
    //         $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;

    //         echo "<div class='col'>";
    //         echo "<div class='row'>";

    //         // Swap seat1 and seat2 every alternate row
    //         if ($i % 2 === 0) {
    //             // Even row: seat1 left, seat2 right
    //             renderStudentBox($seat1);
    //             renderStudentBox($seat2);
    //         } else {
    //             // Odd row: seat2 left, seat1 right
    //             renderStudentBox($seat2);
    //             renderStudentBox($seat1);
    //         }

    //         echo "</div>"; // End seat row
    //         echo "</div>"; // End column
    //     }

    //     echo "</div>"; // End row
    // }
for ($i = 0; $i < $rows; $i++) {
    echo "<div class='row'>";

    $rowColumns = [];

    for ($j = 0; $j < $cols; $j++) {
        $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
        $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;

        ob_start(); // Start output buffering
        echo "<div class='col'>";
        // echo "<div class='row'>";
        echo "<div class='row border g-4'>";
        if ($i % 2 === 0) {
            renderStudentBox($seat1,$saveData,$room,$examDate,$examTime24);
            renderStudentBox($seat2,$saveData,$room,$examDate,$examTime24);
        } else {
            renderStudentBox($seat2,$saveData,$room,$examDate,$examTime24); // reverse inside column
            renderStudentBox($seat1,$saveData,$room,$examDate,$examTime24);
        }
        echo "</div>";
        echo "</div>";
        $rowColumns[] = ob_get_clean(); // Store the column HTML
    }

    // Reverse entire row if odd
    if ($i % 2 !== 0) {
        $rowColumns = array_reverse($rowColumns);
    }

    // Output row
    foreach ($rowColumns as $colHtml) {
        echo $colHtml;
    }

    echo "</div>"; // End row
}

    echo "</div>"; // End container
}
?>
<?php 
function renderStudentBox($seat, $saveData, $room, $examDate, $examTime) {
    echo "<div class='col-md-6x col-lg-4x col'>"; // Responsive columns: 2/row (md), 3/row (lg)

    if ($seat) {
        echo "<div class='student-cardx p-3 borderx roundedx h-100x'>";
        echo "<p class='fs-6'><strong>Roll No:</strong> " . htmlspecialchars($seat['roll_no']) . "</p>";
        echo "<p class='fs-6'><strong>Name:</strong> " . htmlspecialchars($seat['name']) . "</p>";
        echo "<p class='fs-6'><strong>Department:</strong> " . htmlspecialchars($seat['department']) . "</p>";
        echo "<p class='fs-6'><strong>Semester:</strong> " . htmlspecialchars($seat['semester']) . "</p>";
        echo "<p class='fs-6'><strong>Course:</strong> " . htmlspecialchars($seat['course']) . "</p>";

        echo "</div>";

        if (strtoupper(trim($seat['name'])) !== 'NIL') {
            $attendance = new AttendanceSheet();
            if ($saveData == 1) {
                $attendance->insertAttendance(
                    $examDate,
                    $examTime,
                    $seat['roll_no'],
                    $seat['name'],
                    $seat['department'],
                    $seat['semester'],
                    $seat['course'],
                    $room['room_no'],
                    $room['room_name'],
                    $room['banch_order'],
                    0
                );
            }
        }

    } else {
        echo "<div class='text-danger'>No student assigned.</div>";
    }

    echo "</div>";
}

?>