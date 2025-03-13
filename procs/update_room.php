<?php
include('../db/pdo_connect.php');

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['roomNo'], $data['roomName'], $data['size'], $data['capacity'])) {
    $stmt = $pdo->prepare("UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no");
    
    $stmt->execute([
        ':room_no' => $data['roomNo'],
        ':room_name' => $data['roomName'],
        ':bench_order' => $data['size'],  // Fixed column name
        ':seat_capacity' => $data['capacity']
    ]);

    echo json_encode(["message" => "Room updated successfully!"]);
} else {
    echo json_encode(["message" => "Invalid data received."]);
}
?>
