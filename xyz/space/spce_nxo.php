<?php 
include ('../bashmodel.php');

$room = new Room();
$rooms = $room->getAllRoomsJSONS();
echo json_encode($rooms);

?>
