<?php
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$department_id = $_GET['department_id'] ?? '';

// Database setup
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM attendance_sheet WHERE date = :date AND time = :time AND department = :department_id");
$stmt->execute([
    ':date' => $date,
    ':time' => $time,
    ':department_id' => $department_id
]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate present and absent roll numbers
$presentRolls = [];
$absentRolls = [];

foreach ($students as $student) {
    if ($student['student_status'] == 1) {
        $presentRolls[] = $student['roll_no'];
    } else {
        $absentRolls[] = $student['roll_no'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1>SIBSAGAR UNIVERSITY</h1>
        <h2>TOP SHEET</h2>
        <h3>TDC BA 4TH SEMESTER EXAMINATION 2024</h3>
        <h4>SUBJECT: ECONOMICS</h4>
        <h5>PAPER: GECO04T</h5>
        <h6>DATE: <?= htmlspecialchars($date) ?></h6>
        <h6>TIME: <?= htmlspecialchars($time) ?></h6>
    </div>


    <div class="mb-4">
        <h5>CANDIDATE INFORMATION</h5>
        <p><strong>PRESENT ROLL NOS.:</strong> <?= !empty($presentRolls) ? implode(', ', $presentRolls) : 'None' ?></p>
        <p><strong>CANDIDATES ABSENT ROLL NUMBERS:</strong> <?= !empty($absentRolls) ? implode(', ', $absentRolls) : 'None' ?></p>
    </div>

    <div class="text-center">
        <p><strong>Expelled:</strong> <?= date('d/m/y') ?></p>
        <p>All answer scripts should be evaluated and returned along with the original copy of this TOP SHEET to the officer concerned.</p>
    </div>

    <div class="row mb-3">
        <div class="col">
            <h5>PARTICULARS OF IDENTIFICATION</h5>
            <p><strong>Name of Centre:</strong> ______________________</p>
            <p><strong>Date:</strong> <?= htmlspecialchars($date) ?></p>
            <p><strong>Signature of the Officer in-charge:</strong> ______________________</p>
        </div>
        <div class="col">
            <h5>EXAMINATION DETAILS</h5>
            <p><strong>Total No. of Candidates Present:</strong> <?= count($presentRolls) ?></p>
            <p><strong>Total No. of Candidates Absent:</strong> <?= count($absentRolls) ?></p>
            <p><strong>Shift:</strong> 01:00 PM TO 04:00 PM</p>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
