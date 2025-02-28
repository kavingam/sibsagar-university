<?php
include('../db/pdo_connect.php');


try {
    $stmt = $pdo->prepare("SELECT * FROM rooms ORDER BY room_no ASC");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rooms);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
