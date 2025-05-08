
<?php
// session_start();

// Include necessary files
require_once '../Database.php';
require_once '../bashmodel.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the incoming JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the incoming data
    if (!isset($data['roll_no']) || !isset($data['student_status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    // Extract data from the request
    $roll_no = $data['roll_no'];
    $student_status = $data['student_status'];
    $date = $data['date'];
    $time = $data['time'];
    $course = $data['course'];
    $semester = $data['semester'];

    // Create the database connection and initialize the AttendanceSheet model
    $database = Database::getInstance();
    $db = $database->getConnection();
    $attendanceSheet = new AttendanceSheet($db);

    // Update the attendance status in the database
    $result = $attendanceSheet->updateAttendanceStatus($roll_no, $student_status, $date, $time, $course, $semester);

    // Return success or failure response
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Attendance updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update attendance.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

