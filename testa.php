<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zigzag Seating - Balanced</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            background: #f9f9f9;
        }
        .room {
            border: 2px solid #333;
            margin: 20px auto;
            padding: 10px;
            width: fit-content;
            display: inline-block;
            background: #fff;
            border-radius: 8px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .room-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #333;
            color: white;
            padding: 8px;
            border-radius: 5px;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .row {
            display: flex;
            justify-content: center;
            gap: 15px;
            width: 100%;
            flex-wrap: wrap;
        }
        .bench {
            width: 160px;
            height: 80px;
            display: flex;
            border: 3px solid #555;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .bench:hover {
            transform: scale(1.05);
        }
        .seat {
            flex: 1;
            padding: 5px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #333;
        }
        .left-seat {
            background: #ffdddd;
            border-right: 2px solid #555;
        }
        .right-seat {
            background: #ddf0ff;
        }
        .even-row {
            flex-direction: row-reverse;
            background: #fff1f1;
        }
    </style>
</head>
<body>

    <h2>Zigzag Seating - Balanced Distribution</h2>

    <?php
    // Room Configuration
    $rooms = [
        ["name" => "NS-1", "total_benches" => 40, "benches_per_row" => 3, "rows" => 8],
        ["name" => "NS-2", "total_benches" => 30, "benches_per_row" => 2, "rows" => 8]
    ];
    
    // Department & Student Count
    $departments = [
        "CS" => 3, "Math" => 3
    ];

    // Create department-wise queues
    $studentQueues = [];
    foreach ($departments as $dept => $count) {
        for ($i = 1; $i <= $count; $i++) {
            $studentQueues[$dept][] = "$dept-$i";
        }
    }

    // Shuffle each department separately
    foreach ($studentQueues as &$queue) {
        shuffle($queue);
    }
    unset($queue); // Break reference

    // Separate remainder students who cannot be paired
    $remainderStudents = [];
    foreach ($studentQueues as $dept => &$queue) {
        if (count($queue) % 2 != 0) {
            $remainderStudents[] = array_pop($queue); // Move last student to remainder list
        }
    }
    unset($queue);

    // Assign students to benches ensuring no conflicts
    foreach ($rooms as $room) {
        echo "<div class='room'>";
        echo "<div class='room-title'>Room {$room['name']}</div>";
        echo "<div class='container'>";

        $assignedSeats = [];
        $rowGap = 2; // Define gap for remainder students
        
        for ($r = 1; $r <= $room['rows']; $r++) {
            // Add extra gap row for remainder students
            if (!empty($remainderStudents) && $r % ($rowGap + 1) == 0) {
                continue; // Skip this row for spacing
            }
            
            echo '<div class="row ' . ($r % 2 == 0 ? 'even-row' : '') . '">';
            
            for ($b = 1; $b <= $room['benches_per_row']; $b++) {
                
                // Ensure different departments for left & right seats
                $deptKeys = array_keys($studentQueues);
                $deptLeft = $deptKeys[array_rand($deptKeys)];
                
                // Prevent same department from sitting on the same bench
                do {
                    $deptRight = $deptKeys[array_rand($deptKeys)];
                } while ($deptRight === $deptLeft);
                
                // Assign students if available
                $seatInfo1 = !empty($studentQueues[$deptLeft]) ? array_shift($studentQueues[$deptLeft]) : "Empty";
                $seatInfo2 = !empty($studentQueues[$deptRight]) ? array_shift($studentQueues[$deptRight]) : "Empty";

                // Store assigned students to prevent same column conflict
                $assignedSeats[$r][$b] = [$seatInfo1, $seatInfo2];

                echo "<div class='bench'>
                        <div class='seat left-seat'>$seatInfo1</div>
                        <div class='seat right-seat'>$seatInfo2</div>
                     </div>";
            }
            echo '</div>';
        }

        // Assign remaining students after spacing
        if (!empty($remainderStudents)) {
            echo '<div class="row even-row">';
            foreach ($remainderStudents as $student) {
                echo "<div class='bench'>
                        <div class='seat left-seat'>$student</div>
                        <div class='seat right-seat'>Empty</div>
                     </div>";
            }
            echo '</div>';
        }

        echo "</div></div>";
    }
    ?>

</body>
</html>
