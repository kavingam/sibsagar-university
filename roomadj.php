<?php
// Room Class
class Room {
    private $rooms;

    public function __construct() {
        // Sample Rooms Data
        $this->rooms = [
            ["room_no" => "01", "room_name" => "NS-2", "bench_order" => 2, "seat_capacity" => 30],
            ["room_no" => "02", "room_name" => "NS-3", "bench_order" => 2, "seat_capacity" => 40],
            ["room_no" => "03", "room_name" => "NS-4", "bench_order" => 3, "seat_capacity" => 50],
            ["room_no" => "04", "room_name" => "NS-5", "bench_order" => 1, "seat_capacity" => 20],
            ["room_no" => "05", "room_name" => "NS-6", "bench_order" => 3, "seat_capacity" => 60]
        ];
    }

    // Fetch all rooms
    public function getAllRooms() {
        return $this->rooms;
    }
}

// Function to find the nearest or adjusted room
function findBestRoom($rooms, $targetCapacity) {
    $nearestRoom = null;
    $alternativeRooms = [];
    $minDifference = PHP_INT_MAX; // Start with a high difference

    foreach ($rooms as $room) {
        $difference = abs($room['seat_capacity'] - $targetCapacity);

        // Exact match
        if ($room['seat_capacity'] == $targetCapacity) {
            return ['room' => $room, 'adjustment' => 'Exact Fit'];
        }

        // Best nearest match
        if ($difference < $minDifference) {
            $minDifference = $difference;
            $nearestRoom = $room;
        }

        // Store rooms smaller than required (for possible merging)
        if ($room['seat_capacity'] < $targetCapacity) {
            $alternativeRooms[] = $room;
        }
    }

    // If no single room fits, try merging smaller rooms
    $mergedRooms = tryMergeRooms($alternativeRooms, $targetCapacity);
    if ($mergedRooms) {
        return ['room' => $mergedRooms, 'adjustment' => 'Merged Multiple Rooms'];
    }

    return ['room' => $nearestRoom, 'adjustment' => 'Nearest Available Room'];
}

// Function to try merging smaller rooms to fit capacity
function tryMergeRooms($rooms, $targetCapacity) {
    $merged = [];
    $totalSeats = 0;

    foreach ($rooms as $room) {
        $merged[] = $room;
        $totalSeats += $room['seat_capacity'];

        if ($totalSeats >= $targetCapacity) {
            return $merged;
        }
    }

    return null; // No suitable merge found
}

// Fetch rooms from Room class
$roomObj = new Room();
$rooms = $roomObj->getAllRooms();

// Example: User requests a room for 50 students
$targetCapacity = 100;
$bestRoom = findBestRoom($rooms, $targetCapacity);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatic Room Adjustment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Automatic Room Adjustment</h2>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Assigned Room</h5>

            <?php if ($bestRoom['adjustment'] === 'Merged Multiple Rooms'): ?>
                <p><strong>Adjustment Type:</strong> <?= $bestRoom['adjustment']; ?></p>
                <ul class="list-group">
                    <?php foreach ($bestRoom['room'] as $room): ?>
                        <li class="list-group-item">
                            <strong>Room No:</strong> <?= $room['room_no']; ?> - 
                            <strong>Room Name:</strong> <?= $room['room_name']; ?> - 
                            <strong>Seats:</strong> <?= $room['seat_capacity']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <table class="table table-bordered">
                    <tr><th>Room No</th><td><?= $bestRoom['room']['room_no']; ?></td></tr>
                    <tr><th>Room Name</th><td><?= $bestRoom['room']['room_name']; ?></td></tr>
                    <tr><th>Bench Order</th><td><?= $bestRoom['room']['bench_order']; ?></td></tr>
                    <tr><th>Seat Capacity</th><td><?= $bestRoom['room']['seat_capacity']; ?></td></tr>
                    <tr><th>Adjustment Type</th><td><?= $bestRoom['adjustment']; ?></td></tr>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
