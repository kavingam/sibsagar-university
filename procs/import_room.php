<?php 
/* SQL
CREATE TABLE rooms (
    room_no VARCHAR(50) NOT NULL UNIQUE,
    room_name VARCHAR(100) NOT NULL,
    size ENUM('small', 'medium', 'large') NOT NULL,
    seat_capacity INT NOT NULL
);
*/
?>

<?php
include('../db/pdo_connect.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $roomNo = $_POST["roomNo"] ?? "";
    $roomName = $_POST["roomName"] ?? "";
    $size = $_POST["size"] ?? "";
    $seatCapacity = $_POST["seatCapacity"] ?? "";

    if (!empty($roomNo) && !empty($roomName) && !empty($size) && !empty($seatCapacity)) {
        $checkSql = "SELECT room_name FROM rooms WHERE room_no = :roomNo";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(":roomNo", $roomNo);
        $checkStmt->execute();
        $existingRoom = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRoom) {
            echo json_encode(["message" => "Room No already exists. Existing Room Name: " . $existingRoom["room_name"]]);
        } else {
            $insertSql = "INSERT INTO rooms (room_no, room_name, size, seat_capacity) VALUES (:roomNo, :roomName, :size, :seatCapacity)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->bindParam(":roomNo", $roomNo);
            $insertStmt->bindParam(":roomName", $roomName);
            $insertStmt->bindParam(":size", $size);
            $insertStmt->bindParam(":seatCapacity", $seatCapacity);

            if ($insertStmt->execute()) {
                echo json_encode(["message" => "Room added successfully!"]);
            } else {
                echo json_encode(["message" => "Error adding room."]);
            }
        }
    } else {
        echo json_encode(["message" => "All fields are required!"]);
    }
} else {
    echo json_encode(["message" => "Invalid request!"]);
}
?>
