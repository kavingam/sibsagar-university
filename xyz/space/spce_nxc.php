<?php
header("Content-Type: application/json");
include('../bashmodel.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
    exit;
}

// Fetch form data
$roomNo = $_POST["roomNo"] ?? "";
$roomName = $_POST["roomName"] ?? "";
$benchOrder = $_POST["bench_order"] ?? "";
$seatCapacity = $_POST["seatCapacity"] ?? "";

$room = new Room();
$response = $room->createRoomJSON($roomNo, $roomName, $benchOrder, $seatCapacity);
echo json_encode($response);

?>
