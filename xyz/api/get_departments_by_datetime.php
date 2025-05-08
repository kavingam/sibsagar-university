<?php
// Include necessary files
require_once '../bashmodel.php';  // Make sure the path is correct

// Set content type to JSON
header('Content-Type: application/json');

// Get date and time from the request parameters
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

// Validate the parameters
if (!$date || !$time) {
    echo json_encode(["status" => "error", "message" => "Date and time are required"]);
    exit;
}

// Instantiate the AttendanceSheet class (or your relevant class that handles department data)
$attendance = new AttendanceSheet();

// Fetch the departments for the given date and time
$departments = $attendance->getDepartmentsByDateTime($date, $time);

// Return the data as JSON
echo json_encode($departments);
?>
