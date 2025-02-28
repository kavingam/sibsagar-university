<?php
include('../db/pdo_connect.php');

$data = json_decode(file_get_contents("php://input"), true);
if ($data) {
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_no = :room_no");
    $stmt->execute([':room_no' => $data['roomNo']]);

    echo json_encode(["message" => "Room deleted successfully!"]);
} else {
    echo json_encode(["message" => "Invalid request."]);
}
?>
