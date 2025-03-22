<?php
include('../bashmodel.php');

try {
    $room = new Room();
    $rooms = $room->getAllRooms();
    echo json_encode($rooms);
} catch (Exception $e) {
    // Handle errors gracefully
    echo "Error: " . $e->getMessage();
}
?>
