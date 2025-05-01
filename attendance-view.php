<?php
// Set JSON header for proper JavaScript fetch() behavior
// header('Content-Type: application/json');
header('Content-Type: text/html');

// Validate required GET parameters
if (!isset($_GET['date'], $_GET['time'], $_GET['room_no'])) {
    echo json_encode(["error" => "Missing parameters."]);
    exit;
}

$date = $_GET['date'];
$time = $_GET['time'];
$room_no = $_GET['room_no'];

// Load required files
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

try {
    // Get database connection from singleton
    $database = Database::getInstance();
    $db = $database->getConnection();

    // Create model instance and fetch student data
    $attendanceSheet = new AttendanceSheet($db);
    $students = $attendanceSheet->getStudentsByRoom($date, $time, $room_no);

    if (is_array($students)) {
        // echo 
        json_encode($students); // ✅ Valid JSON array
    } else {
        // echo 
        json_encode(["error" => "No student data returned or query failed."]);
    }
} catch (Exception $e) {
    // If there's an exception, return as JSON error
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    
</style>
</head>
<body class="p-4">

<div class="container">
    <h3 class="text-center mb-4">Attendance Sheet</h3>
    <p><strong>Date:</strong> <?= htmlspecialchars($date) ?> | <strong>Time:</strong> <?= htmlspecialchars($time) ?> | <strong>Room:</strong> <?= htmlspecialchars($room_no) ?></p>

    <?php
    // Group students by department
    $groupedStudents = [];
    foreach ($students as $student) {
        $groupedStudents[$student['department']][] = $student;
    }

    // Loop through departments and display students
    foreach ($groupedStudents as $department => $studentsList):
    ?>
        <!-- Department Title -->
        <h4 class="mt-5"><?= htmlspecialchars($department) ?> Department</h4>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentsList as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['roll_no']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Remove any extra page gap by setting no margin after table -->
        <div style="page-break-after: avoid;"></div>

    <?php endforeach; ?>

    <!-- Print Button -->
    <button onclick="window.print()" class="btn btn-success mt-3" id="printButton">Print</button>

    <?php if (empty($students)): ?>
        <div class="alert alert-warning">No students found.</div>
    <?php endif; ?>
</div>


</body>
</html>
