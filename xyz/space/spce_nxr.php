<?php
header("Content-Type: application/json");
include ('../bashmodel.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$room_no = $data["roomNo"] ?? "";

if (empty($room_no)) {
    echo json_encode(["success" => false, "message" => "Room No is required!"]);
    exit;
}

try {
    $room = new Room();
    if ($room->deleteRoomJSON($room_no)) {
        echo json_encode(["success" => true, "message" => "Room deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete room!"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
