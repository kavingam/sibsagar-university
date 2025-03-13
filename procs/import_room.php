<?php 
/*
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("submitBtn").addEventListener("click", function () {
        let roomNo = document.getElementById("roomNo").value.trim();
        let roomName = document.getElementById("roomName").value.trim();
        let bench_order = document.getElementById("bench_order").value;
        let seatCapacity = document.getElementById("seatCapacity").value.trim();

        // Basic validation
        if (!roomNo || !roomName || !bench_order || !seatCapacity) {
            alert("Please fill out all fields.");
            return;
        }

        let formData = new FormData();
        formData.append("roomNo", roomNo);
        formData.append("roomName", roomName);
        formData.append("bench_order", bench_order);
        formData.append("seatCapacity", seatCapacity);

        fetch("procs/import_room.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                document.getElementById("roomNo").value = "";
                document.getElementById("roomName").value = "";
                document.getElementById("bench_order").value = "2";
                document.getElementById("seatCapacity").value = "";
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while processing your request.");
        });
    });
});
*/
?>

<?php
// include('../db/pdo_connect.php');

// header("Content-Type: application/json");

// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//     $roomNo = trim($_POST["roomNo"] ?? "");
//     $roomName = trim($_POST["roomName"] ?? "");
//     $benchOrder = $_POST["bench_order"] ?? "";
//     $seatCapacity = $_POST["seatCapacity"] ?? "";

//     if (empty($roomNo) || empty($roomName) || empty($benchOrder) || empty($seatCapacity)) {
//         echo json_encode(["success" => false, "message" => "All fields are required!"]);
//         exit;
//     }

//     if (!is_numeric($benchOrder) || !is_numeric($seatCapacity)) {
//         echo json_encode(["success" => false, "message" => "Invalid input! Bench Order and Seat Capacity must be numbers."]);
//         exit;
//     }

//     try {
//         // Check if the room already exists
//         $checkSql = "SELECT room_name FROM rooms WHERE room_no = :roomNo";
//         $checkStmt = $pdo->prepare($checkSql);
//         $checkStmt->bindParam(":roomNo", $roomNo, PDO::PARAM_STR);
//         $checkStmt->execute();
//         $existingRoom = $checkStmt->fetch(PDO::FETCH_ASSOC);

//         if ($existingRoom) {
//             echo json_encode([
//                 "success" => false, 
//                 "message" => "Room No already exists. Existing Room Name: " . htmlspecialchars($existingRoom["room_name"])
//             ]);
//             exit;
//         }

//         // Insert new room
//         $insertSql = "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) VALUES (:roomNo, :roomName, :benchOrder, :seatCapacity)";
//         $insertStmt = $pdo->prepare($insertSql);
//         $insertStmt->bindParam(":roomNo", $roomNo, PDO::PARAM_STR);
//         $insertStmt->bindParam(":roomName", $roomName, PDO::PARAM_STR);
//         $insertStmt->bindParam(":benchOrder", $benchOrder, PDO::PARAM_INT);
//         $insertStmt->bindParam(":seatCapacity", $seatCapacity, PDO::PARAM_INT);

//         if ($insertStmt->execute()) {
//             echo json_encode(["success" => true, "message" => "Room added successfully!"]);
//         } else {
//             echo json_encode(["success" => false, "message" => "Error adding room."]);
//         }
//     } catch (PDOException $e) {
//         echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
//     }
// } else {
//     echo json_encode(["success" => false, "message" => "Invalid request!"]);
// }
?>
<?php
include('../db/pdo_connect.php');

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
    exit;
}

$roomNo = trim($_POST["roomNo"] ?? "");
$roomName = trim($_POST["roomName"] ?? "");
$benchOrder = $_POST["bench_order"] ?? "";
$seatCapacity = $_POST["seatCapacity"] ?? "";

// Basic validation
if (empty($roomNo) || empty($roomName) || empty($benchOrder) || empty($seatCapacity)) {
    echo json_encode(["success" => false, "message" => "All fields are required!"]);
    exit;
}

if (!is_numeric($benchOrder) || !is_numeric($seatCapacity)) {
    echo json_encode(["success" => false, "message" => "Bench Order and Seat Capacity must be numbers."]);
    exit;
}

try {
    // Check if the room already exists
    $checkSql = "SELECT room_name FROM rooms WHERE room_no = :roomNo";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([":roomNo" => $roomNo]);
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
    $insertStmt->execute([
        ":roomNo" => $roomNo,
        ":roomName" => $roomName,
        ":benchOrder" => $benchOrder,
        ":seatCapacity" => $seatCapacity
    ]);

    echo json_encode(["success" => true, "message" => "Room added successfully!"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>