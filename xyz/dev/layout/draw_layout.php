<?php
// Fetch and flatten student list
$data = $seatAllocationStore->findAll();
$students = [];

foreach ($data as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

echo '<pre>';
// print_r($students);
echo '</pre>';
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
?>

<?php
$student_index = 0;

foreach ($assignedRooms as $room) {
    $total_seats = $room['students_assigned'];
    $cols = $room['banch_order']; // Number of columns
    $rows = ceil($total_seats / ($cols * 2)); // 2 seats per column

    echo "<div class='container mb-5'>";
    echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>" . htmlspecialchars($room['room_no']) . "</strong> - " . htmlspecialchars($room['room_name']) . "</h4>";

    for ($i = 0; $i < $rows; $i++) {
        echo "<div class='row gx-4 gy-3'>";

        for ($j = 0; $j < $cols; $j++) {
            $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
            $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;

            echo "<div class='col'>";
            echo "<div class='row'>";

            // Swap seat1 and seat2 every alternate row
            if ($i % 2 === 0) {
                // Even row: seat1 left, seat2 right
                renderStudentBox($seat1);
                renderStudentBox($seat2);
            } else {
                // Odd row: seat2 left, seat1 right
                renderStudentBox($seat2);
                renderStudentBox($seat1);
            }

            echo "</div>"; // End seat row
            echo "</div>"; // End column
        }

        echo "</div>"; // End row
    }

    echo "</div>"; // End container
}

// Helper function to render student box
function renderStudentBox($seat) {
    echo "<div class='col p-2 border fs-6'>";
    if ($seat) {
        echo "<strong>Roll No:</strong> " . htmlspecialchars($seat['roll_no']) . "<br>";
        echo "<strong>Name:</strong> " . htmlspecialchars($seat['name']) . "<br>";
        echo "<strong>Department:</strong> " . htmlspecialchars($seat['department']) . "<br>";
        echo "<strong>Semester:</strong> " . htmlspecialchars($seat['semester']) . "<br>";
        echo "<strong>Course:</strong> " . htmlspecialchars($seat['course']) . "<br>";
    } else {
        echo "No student assigned.";
    }
    echo "</div>";
}
?>
