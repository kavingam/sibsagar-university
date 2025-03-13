<?php
include('../db/pdo_connect.php');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['roomNo'])) {
    echo json_encode(["success" => false, "message" => "Invalid request. Room number is required."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_no = :room_no");
    $stmt->execute([':room_no' => $data['roomNo']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Room deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Room not found or already deleted."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error deleting room: " . $e->getMessage()]);
}
?>

