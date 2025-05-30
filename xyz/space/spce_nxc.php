<?php
header("Content-Type: application/json");
include('../bashmodel.php');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request! Only POST allowed."]);
    exit;
}

// Required fields
$roomName     = !empty($_POST["roomName"])     ? $_POST["roomName"]     : null;
$benchOrder   = !empty($_POST["bench_order"])  ? $_POST["bench_order"]  : null;
$seatCapacity = !empty($_POST["seatCapacity"]) ? $_POST["seatCapacity"] : null;

// Exit if any required field is missing
if (!$roomName || !$benchOrder || !$seatCapacity) {
    echo json_encode([
        "success" => false,
        "message" => "Room Name, Bench Order, and Seat Capacity are required."
    ]);
    exit;
}

// Optional field
// $roomNo = $_POST["roomNo"] ?? "null";
$roomNo = !empty($_POST["roomNo"]) ? $_POST["roomNo"] : "null";


// Create room
$room = new Room();
$response = $room->createRoomJSON($roomNo, $roomName, $benchOrder, $seatCapacity);

// Return response
echo json_encode($response);
?>
