<?php 
/* SQL
CREATE TABLE rooms (
    room_no VARCHAR(50) NOT NULL UNIQUE,
    room_name VARCHAR(100) NOT NULL,
    bench_order INT NOT NULL,
    seat_capacity INT NOT NULL
);
*/
?>

<?php
include('../db/pdo_connect.php');

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $roomNo = trim($_POST["roomNo"] ?? "");
    $roomName = trim($_POST["roomName"] ?? "");
    $benchOrder = $_POST["bench_order"] ?? "";
    $seatCapacity = $_POST["seatCapacity"] ?? "";

    if (empty($roomNo) || empty($roomName) || empty($benchOrder) || empty($seatCapacity)) {
        echo json_encode(["success" => false, "message" => "All fields are required!"]);
        exit;
    }

    if (!is_numeric($benchOrder) || !is_numeric($seatCapacity)) {
        echo json_encode(["success" => false, "message" => "Invalid input! Bench Order and Seat Capacity must be numbers."]);
        exit;
    }

    try {
        // Check if the room already exists
        $checkSql = "SELECT room_name FROM rooms WHERE room_no = :roomNo";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(":roomNo", $roomNo, PDO::PARAM_STR);
        $checkStmt->execute();
        $existingRoom = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRoom) {
            echo json_encode([
                "success" => false, 
                "message" => "Room No already exists. Existing Room Name: " . htmlspecialchars($existingRoom["room_name"])
            ]);
            exit;
        }

        // Insert new room
        $insertSql = "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) VALUES (:roomNo, :roomName, :benchOrder, :seatCapacity)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->bindParam(":roomNo", $roomNo, PDO::PARAM_STR);
        $insertStmt->bindParam(":roomName", $roomName, PDO::PARAM_STR);
        $insertStmt->bindParam(":benchOrder", $benchOrder, PDO::PARAM_INT);
        $insertStmt->bindParam(":seatCapacity", $seatCapacity, PDO::PARAM_INT);

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Room added successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error adding room."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
}
?>
