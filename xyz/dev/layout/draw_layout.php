<?php
// Fetch and flatten student list
$data = $seatAllocationStore->findAll();
$students = [];

foreach ($data as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

// Allocate students to rooms
$assignedRooms = allocateStudentsToRooms($rooms, count($students));

// Academic details
$academicYear = date('Y');
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
// Helper function to render seat
function renderSeat($seat, $room, $allDept, $examName, $academicYear, $saveData, $attendance, $examDate, $examTime24)
{
    if ($seat) {
        if (isset($seat['semester']) && $seat['semester'] === 'NIL') {
            return "<div class='fw-semibold fs-3'>NIL</div>";
        } else {
            if ($saveData == 1) {
                $attendance->insertAttendance(
                    $examDate,
                    $examTime24,
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

            $deptName = htmlspecialchars(getDepartmentNameById($allDept, $seat['department']));

            return "<strong>SEM: " . htmlspecialchars($seat['semester']) . "<sup>TH</sup> SEM " . 
                   htmlspecialchars($examName) . " - " . 
                   htmlspecialchars($academicYear) . "</strong><br>
                    <strong>(UNDER AUTONOMOUS)</strong><br>
                    <span>SL NO: " . htmlspecialchars($room['room_no']) . 
                   " ROOM: " . htmlspecialchars($room['room_name']) . "</span><br>
                    <strong>ROLL NO: " . htmlspecialchars($seat['roll_no']) . "</strong><br>
                    <strong>DEPT: " . $deptName . "</strong><br>";
        }
    } else {
        return "<div class='fw-semibold fs-3'>NIL</div>";
    }
}

$student_index = 0;

foreach ($assignedRooms as $room) {
    $cols = $room['banch_order']; // Benches per row
    $total_seats = $room['students_assigned'];
    $rows = ceil($total_seats / ($cols * 2));

    echo "<div class='container mb-5'>";
    echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>" . htmlspecialchars($room['room_no']) . "</strong> - " . htmlspecialchars($room['room_name']) . "</h4>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered text-center align-middle'>";

    for ($i = 0; $i < $rows; $i++) {
        echo "<tr>";

        for ($j = 0; $j < $cols; $j++) {
            echo "<td style='min-width:180px;' class='text-nowrap text-center'>";

            if ($i % 2 == 1) {
                $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;
                $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
            } else {
                $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
                $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;
            }

            echo "<div class='container'><div class='row p-2'>";

            // Seat 1
            echo "<div class='col-6 col-md-6 box text-center border p-2 fs-7'>";
            echo renderSeat($seat1, $room, $allDept, $examName, $academicYear, $saveData, $attendance, $examDate, $examTime24);
            echo "</div>";

            // Seat 2
            echo "<div class='col-6 col-md-6 box text-center border p-2 fs-7'>";
            echo renderSeat($seat2, $room, $allDept, $examName, $academicYear, $saveData, $attendance, $examDate, $examTime24);
            echo "</div>";

            echo "</div></div>"; // end row and container
            echo "</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
    echo "</div></div>"; // end table and container
}
?>
