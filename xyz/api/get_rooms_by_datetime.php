<?php
require_once '../bashmodel.php';
$attendance = new AttendanceSheet();

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$rooms = [];

if ($date && $time) {
    $rooms = $attendance->getRoomsByDateTime($date, $time); // define this
}

header('Content-Type: application/json');
echo json_encode($rooms);
