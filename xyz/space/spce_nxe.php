<?php
header("Content-Type: application/json");
require_once '../bashmodel.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method!"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$roomNo = $data["roomNo"] ?? "";
$roomName = $data["roomName"] ?? "";
$benchOrder = $data["benchOrder"] ?? "";
$seatCapacity = $data["seatCapacity"] ?? "";

if (empty($roomNo) || empty($roomName) || empty($benchOrder) || empty($seatCapacity)) {
    echo json_encode(["success" => false, "message" => "All fields are required!"]);
    exit;
}

try {
    $room = new Room();
    $updated = $room->updateRoom($roomNo, $roomName, $benchOrder, $seatCapacity);

    echo json_encode([
        "success" => $updated, 
        "message" => $updated ? "Room updated successfully!" : "Failed to update room."
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
