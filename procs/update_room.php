<?php
// function updateRoom() {
//     let roomNo = document.getElementById("editRoomNo").value;
//     let roomName = document.getElementById("editRoomName").value;
//     let size = document.getElementById("editSize").value;
//     let capacity = document.getElementById("editCapacity").value;

//     fetch("procs/update_room.php", {
//         method: "POST",
//         headers: { "Content-Type": "application/json" },
//         body: JSON.stringify({ roomNo, roomName, size, capacity })
//     })
//     .then(response => response.json())
//     .then(data => {
//         alert(data.message);
//         if (data.success) {
//             location.reload();  // Full page reload
//         }
//     })
//     .catch(error => console.error("Error updating room:", error));
// }
// include('../db/pdo_connect.php');

// $data = json_decode(file_get_contents("php://input"), true);

// if ($data && isset($data['roomNo'], $data['roomName'], $data['size'], $data['capacity'])) {
//     $stmt = $pdo->prepare("UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no");
    
//     $stmt->execute([
//         ':room_no' => $data['roomNo'],
//         ':room_name' => $data['roomName'],
//         ':bench_order' => $data['size'],  // Fixed column name
//         ':seat_capacity' => $data['capacity']
//     ]);

//     echo json_encode(["message" => "Room updated successfully!"]);
// } else {
//     echo json_encode(["message" => "Invalid data received."]);
// }
?>
<?php
include('../db/pdo_connect.php');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['roomNo'], $data['roomName'], $data['size'], $data['capacity'])) {
    echo json_encode(["success" => false, "message" => "Invalid data received."]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no");
    
    $stmt->execute([
        ':room_no' => $data['roomNo'],
        ':room_name' => $data['roomName'],
        ':bench_order' => $data['size'],  // Fixed column name
        ':seat_capacity' => $data['capacity']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Room updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "No changes made or room not found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error updating room: " . $e->getMessage()]);
}
?>
