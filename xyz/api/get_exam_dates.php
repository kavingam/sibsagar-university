<?php
require_once '../bashmodel.php';
$attendance = new AttendanceSheet();
$dates = $attendance->getAllExamDates(); // returns DISTINCT dates only
header('Content-Type: application/json');
echo json_encode($dates);
