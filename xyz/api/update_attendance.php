<?php
// Include necessary files
require_once '../Database.php';
require_once '../bashmodel.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the incoming JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the incoming data
    if (!isset($data['roll_no']) || !isset($data['student_status']) || !isset($data['date']) || !isset($data['time']) || !isset($data['department_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    // Extract data from the request
    $roll_no = $data['roll_no'];
    $student_status = $data['student_status'];
    $date = $data['date'];
    $time = $data['time'];
    $department_id = $data['department_id'];

    // Create the database connection and initialize the AttendanceSheet model
    $database = Database::getInstance();
    $db = $database->getConnection();
    $attendanceSheet = new AttendanceSheet($db);

    // Prepare SQL UPDATE query to update attendance status
    $sql = "UPDATE attendance_sheet 
            SET student_status = :student_status 
            WHERE roll_no = :roll_no 
            AND date = :date 
            AND time = :time 
            AND department = :department_id";

    // Prepare the statement
    $stmt = $db->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':student_status', $student_status);
    $stmt->bindParam(':roll_no', $roll_no);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':department_id', $department_id);

    // Execute the query and check if the update is successful
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Attendance updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update attendance.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
