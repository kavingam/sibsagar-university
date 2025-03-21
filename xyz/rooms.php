<?php
require_once 'Database.php';

class Room {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Insert a new room
    public function insertRoom($room_no, $room_name, $bench_order, $seat_capacity) {
        $sql = "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) VALUES (:room_no, :room_name, :bench_order, :seat_capacity)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':room_no' => $room_no,
            ':room_name' => $room_name,
            ':bench_order' => $bench_order,
            ':seat_capacity' => $seat_capacity
        ]);
    }

    // Retrieve all rooms
    public function getRooms() {
        $sql = "SELECT * FROM rooms";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a room
    public function updateRoom($room_no, $room_name, $bench_order, $seat_capacity) {
        $sql = "UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':room_no' => $room_no,
            ':room_name' => $room_name,
            ':bench_order' => $bench_order,
            ':seat_capacity' => $seat_capacity
        ]);
    }

    // Delete a room
    public function deleteRoom($room_no) {
        $sql = "DELETE FROM rooms WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':room_no' => $room_no]);
    }
    
    // 🔥 Delete all rooms
    public function deleteAllRooms() {
        $sql = "DELETE FROM rooms"; // WARNING: This will remove all data
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(); 
    }   
}
?>

<?php
// require_once 'Room.php';

// $room = new Room();

// Insert Room
// $room->insertRoom('R101', 'Physics Lab', 1, 50);
// Get All Rooms
// $rooms = $room->getRooms();
// print_r($rooms);

// Update Room
// $room->updateRoom('R101', 'Advanced Physics Lab', 2, 60);

// Delete Room
// $room->deleteRoom('R101');
?>

<?php
// require_once 'Room.php';

// $room = new Room();
// 
// // Delete all rooms
// if ($room->deleteAllRooms()) {
    // echo "✅ All rooms deleted successfully!";
// } else {
    // echo "❌ Failed to delete rooms.";
// }
?>
