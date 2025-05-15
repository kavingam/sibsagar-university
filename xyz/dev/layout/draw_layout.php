<?php
// Fetch and flatten student list
$data = $seatAllocationStore->findAll();
$students = [];

foreach ($data as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

// Academic details
$academicYear = date('Y');

// Group students by subject
$subjectWiseStudents = [];
foreach ($students as $student) {
    if (!isset($student['subject']) || empty(trim($student['subject']))) {
        continue; // Skip if no subject
        // OR: $subject = 'Unknown'; to keep them
    } else {
        $subject = trim($student['subject']);
    }

    if (!isset($subjectWiseStudents[$subject])) {
        $subjectWiseStudents[$subject] = [];
    }

    $subjectWiseStudents[$subject][] = $student;
}


// Find max rows based on subject list lengths
$maxRows = 0;
foreach ($subjectWiseStudents as $subject => $list) {
    $maxRows = max($maxRows, count($list));
}
?>

<!-- Header Info -->
<div class="container-fluid text-center my-5">
    <img src="assets/Picture-1.png" class="img-fluid" alt="Picture 1">
    <p class="fs-5 fw-bold mt-2">Student Seat Allotment List</p>
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

<!-- Seat Layout -->
<div class="container-fluid mb-5">
    <h4 class="mb-3 fs-6 fw-bold">Room No: <strong><?= htmlspecialchars($room['room_no']) ?></strong> - <?= htmlspecialchars($room['room_name']) ?></h4>
    
    <div class="row text-center fw-bold mb-2">
        <?php foreach ($subjectWiseStudents as $subject => $list): ?>
            <div class="col"><?= htmlspecialchars($subject) ?></div>
        <?php endforeach; ?>
    </div>

    <?php for ($i = 0; $i < $maxRows; $i++): ?>
        <div class="row">
            <?php foreach ($subjectWiseStudents as $subject => $list): ?>
                <div class="col border p-2">
                    <?php 
                        $seat = $list[$i] ?? null;
                        renderStudentBox($seat, $saveData, $room, $examDate, $examTime24);
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endfor; ?>
</div>

<?php 
function renderStudentBox($seat, $saveData, $room, $examDate, $examTime) {
    $department = new Department();
    echo "<div class='small text-start'>";

    if ($seat) {
        echo "<div class='student-cardx'>";
        echo "<p class='mb-1'><strong>Roll No:</strong> " . htmlspecialchars($seat['roll_no']) . "</p>";
        echo "<p class='mb-1'><strong>Name:</strong> " . htmlspecialchars($seat['name']) . "</p>";

        $deptName = 'NIL';
        if (!empty($seat['department']) && strtoupper(trim($seat['department'])) !== 'NIL') {
            $fetched = $department->getDepartmentName($seat['department']);
            if ($fetched) {
                $deptName = $fetched;
            }
        }

        echo "<p class='mb-1'><strong>Department:</strong> " . htmlspecialchars($deptName) . "</p>";
        echo "<p class='mb-1'><strong>Semester:</strong> " . htmlspecialchars($seat['semester']) . "</p>";
        echo "<p class='mb-1'><strong>Course:</strong> " . htmlspecialchars($seat['course']) . "</p>";
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
        echo "<div class='text-muted'>Empty</div>";
    }

    echo "</div>";
}
?>
