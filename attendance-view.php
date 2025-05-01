<?php
// Set JSON header for proper JavaScript fetch() behavior
// header( 'Content-Type: application/json' );
header( 'Content-Type: text/html' );

// Validate required GET parameters
if ( !isset( $_GET[ 'date' ], $_GET[ 'time' ], $_GET[ 'room_no' ] ) ) {
    echo json_encode( [ 'error' => 'Missing parameters.' ] );
    exit;
}

$date = $_GET[ 'date' ];
$time = $_GET[ 'time' ];
$room_no = $_GET[ 'room_no' ];

// Load required files
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

try {
    // Get database connection from singleton
    $database = Database::getInstance();
    $db = $database->getConnection();

    // Create model instance and fetch student data
    $attendanceSheet = new AttendanceSheet( $db );

    $deptObj = new Department( $db );
    $getAllDepartments = $deptObj->getAllDepartments();

    // print_r( $getAllDepartments );
    // Debugging line to check department data

    $students = $attendanceSheet->getStudentsByRoom( $date, $time, $room_no );

    if ( is_array( $students ) ) {
        // echo
        json_encode( $students );
        // ✅ Valid JSON array
    } else {
        // echo
        json_encode( [ 'error' => 'No student data returned or query failed.' ] );
    }
} catch ( Exception $e ) {
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
        <div class="d-flex justify-content-center align-items-center" style="height: 300pxx;">
            <img src="assets/Picture-1.png" alt="Centered Image">
        </div>

        <h3 class="text-center mt-4">Attendance Sheet</h3>
        <p><strong>Date:</strong> <?= htmlspecialchars($date) ?> | <strong>Time:</strong> <?= htmlspecialchars($time) ?>
            | <strong>S NO:</strong> <?= htmlspecialchars($room_no) ?></p>
        <?php
// Fetch department list (replace $db with your actual class instance)


// Map department ID to department name
$departmentNames = [];
foreach ($getAllDepartments as $dept) {
    $departmentNames[$dept['department_id']] = $dept['department_name'];
}

// Group students by department_id
$groupedStudents = [];
foreach ($students as $student) {
    $groupedStudents[$student['department']][] = $student; // assuming 'department' holds department_id
}
?>

        <!-- Loop through grouped students by department -->
        <?php foreach ($groupedStudents as $departmentId => $studentsList): 
        $departmentName = $departmentNames[$departmentId] ?? 'Unknown';
    ?>
        <!-- Department Title -->
        <h4 class="mt-5"><?= htmlspecialchars($departmentName) ?> Department</h4>

        <!-- Student Table -->
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
                    <td><?= htmlspecialchars($student['name' ] ) ?></td>
                    <td></td>
                </tr>
                <?php endforeach;
    ?>
            </tbody>
        </table>

        <div style='page-break-after: avoid;'></div>
        <?php endforeach;
    ?>
        <!-- Print Button -->
        <button onclick='window.print()' class='btn btn-success mt-3' id='printButton'>Print</button>

        <?php if ( empty( $students ) ): ?>
        <div class='alert alert-warning'>No students found.</div>
        <?php endif;
    ?>
    </div>

</body>

</html>