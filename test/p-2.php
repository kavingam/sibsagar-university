<?php
function arrangeSeats($students, $benchesPerRoom = 5) {
    $benches = [];
    $index = 0;
    
    while ($index < count($students)) {
        $bench = [];
        if ($index < count($students)) $bench[] = $students[$index];
        if ($index + 1 < count($students)) $bench[] = $students[$index + 1];
        
        $benches[] = $bench;
        $index += 2;
    }
    
    $totalRooms = ceil(count($benches) / $benchesPerRoom);
    $roomAllocations = array_chunk($benches, $benchesPerRoom);
    
    return ['rooms' => $roomAllocations, 'totalRooms' => $totalRooms];
}

$students = [
    "MCA-Ex-1", "MBA-Ex-1", "MCA-Ex-2", "MBA-Ex-2", "MCA-Ex-3", "MBA-Ex-3",
    "MCA-Ex-4", "MBA-Ex-4", "MCA-Ex-5", "MBA-Ex-5", "MCA-Ex-6", "MBA-Ex-6",
    "MCA-Ex-7", "MBA-Ex-7", "MCA-Ex-8", "MBA-Ex-8", "MCA-Ex-9", "MBA-Ex-9",
    "MCA-Ex-10", "MBA-Ex-10", "MCA-Ex-11", "MBA-Ex-11", "MCA-Ex-12", "MBA-Ex-12"
];

$seatArrangement = arrangeSeats($students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Arrangement</title>
    <style>
        table { width: 50%; border-collapse: collapse; margin-bottom: 20px; }
        td, th { border: 1px solid black; padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <h2>Seat Arrangement</h2>
    <p>Total Rooms Required: <?php echo $seatArrangement['totalRooms']; ?></p>
    
    <?php foreach ($seatArrangement['rooms'] as $roomIndex => $room) { ?>
        <h3>Room <?php echo $roomIndex + 1; ?></h3>
        <table>
            <tr>
                <th>Left Bench</th>
                <th>Right Bench</th>
            </tr>
            <?php 
            for ($i = 0; $i < count($room); $i += 2) { 
                echo "<tr>";
                echo "<td>" . implode(", ", $room[$i]) . "</td>";
                echo "<td>" . (isset($room[$i + 1]) ? implode(", ", $room[$i + 1]) : "") . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php } ?>
</body>
</html>
