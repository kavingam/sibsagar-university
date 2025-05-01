<?php
require_once '../bashmodel.php';
$attendance = new AttendanceSheet();

$date = $_GET['date'] ?? '';
$data = [];

if ($date) {
    $all = $attendance->getEventsByDate($date); // returns times
    foreach ($all as $row) {
        $data[] = [
            'time' => $row['time'],
            'session' => $attendance->getSessionLabel($row['time']),
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data);