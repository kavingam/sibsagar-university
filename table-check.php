<?php
// Load all student JSON files from 'jsons' directory
$directory = new RecursiveDirectoryIterator('jsons');
$iterator = new RecursiveIteratorIterator($directory);
$allStudentData = [];

// Read JSON files from directory
foreach ($iterator as $fileInfo) {
    if ($fileInfo->isFile() && $fileInfo->getExtension() === 'json') {
        $filename = $fileInfo->getPathname();
        $jsonString = file_get_contents($filename);
        $studentData = json_decode($jsonString, true);
        if ($studentData !== null) {
            $allStudentData[] = $studentData;
        } else {
            echo "Error decoding JSON from file: $filename\n";
        }
    }
}

// Merge student groups based on rules
$mergedGroups = [];
$processedDepartments = [];

// Iterate through all departments
foreach ($allStudentData as $index => $dept1) {
    if (in_array($index, $processedDepartments)) {
        continue; // Skip already processed departments
    }

    $matchedDepartments = [$dept1]; // Start with current department
    $processedDepartments[] = $index;

    // Check for other matching departments
    for ($j = $index + 1; $j < count($allStudentData); $j++) {
        $dept2 = $allStudentData[$j];

        // Ensure departments have different courses but same student count
        if (
            $dept1["department"] !== $dept2["department"] &&
            $dept1["semester"] === $dept2["semester"] &&
            $dept1["totalStudent"] === $dept2["totalStudent"]
        ) {
            $matchedDepartments[] = $dept2;
            $processedDepartments[] = $j;
        }
    }

    // If at least two departments match, merge them in zigzag order
    if (count($matchedDepartments) > 1) {
        $mergedStudents = [];
        $numStudents = $matchedDepartments[0]["totalStudent"];

        for ($i = 0; $i < $numStudents; $i++) {
            foreach ($matchedDepartments as $dept) {
                $mergedStudents[] = $dept["students"][$i];
            }
        }

        // Create new merged department
        $mergedGroups[] = [
            "department" => "Merged",
            "semester" => $dept1["semester"],
            "course" => "Mixed",
            "totalStudent" => count($mergedStudents),
            "students" => $mergedStudents
        ];
    }
}

// Load Room Data
$roomJson = file_get_contents('rooms.json');
$rooms = json_decode($roomJson, true)['room'] ?? [];

$seatIndex = 0;
$seatsPerBench = 2; // Each bench has 2 seats (Left & Right)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Allocation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .bench {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
            border-radius: 5px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .seat {
            display: inline-block;
            padding: 8px;
            border: 2px solid #000;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border-radius: 3px;
            width: 80px;
            margin: 5px;
        }
        .seat.left {
            background-color: #32CD32; /* Green */
        }
        .seat.right {
            background-color: #87CEEB; /* Sky Blue */
        }
        .seat.empty {
            background-color: #ccc;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Room Allocation</h2>

    <!-- <?php if (!empty($mergedGroups)): ?>
        <h4 class="mt-4">Seating Arrangement for Merged Students</h4>

        <?php foreach ($rooms as $room): ?>
            <h5 class="mt-3"><?= htmlspecialchars($room['room_name']); ?> - Seating Layout</h5>
            <div class="table-responsive">
                <?= generateSeatingArrangement($room, $mergedGroups, $seatsPerBench, $seatIndex); ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-danger text-center">No merged student groups found.</div>
    <?php endif; ?> -->
</div>

<?php
// Function to generate seating arrangement room-wise
function generateSeatingArrangement($room, &$mergedGroups, $seatsPerBench, &$seatIndex) {
    ob_start();

    $columns = $room['bench_order'] ?? 1; // Number of bench columns
    $totalSeats = $room['seat_capacity'] ?? 1; // Total seat capacity
    $totalBenches = ceil($totalSeats / 2); // Each bench has 2 seats
    $rows = ceil($totalBenches / $columns); // Rows required to arrange benches

    echo "<table class='table table-bordered text-center'><tbody>";

    $benchIndex = 1;
    for ($r = 0; $r < $rows; $r++) {
        echo "<tr>";
        for ($c = 0; $c < $columns; $c++) {
            if ($benchIndex <= $totalBenches) {
                $seats = [];

                // Assign students per bench (Left & Right seat)
                for ($s = 0; $s < $seatsPerBench; $s++) {
                    if ($seatIndex < count($mergedGroups[0]['students'])) {
                        $seats[] = '<span class="seat ' . ($s % 2 == 0 ? 'left' : 'right') . '">' . htmlspecialchars($mergedGroups[0]['students'][$seatIndex]['roll_no']) . '</span>';
                        $seatIndex++;
                    } else {
                        $seats[] = '<span class="seat empty">Empty</span>'; // Empty seat if no student left
                    }
                }

                // Display assigned students for a single bench
                echo "<td class='bench'><strong>Bench $benchIndex</strong><br>" . implode(" & ", $seats) . "</td>";
                $benchIndex++;
            } else {
                echo "<td></td>"; // Empty cell if no more benches left
            }
        }
        echo "</tr>";
    }
    echo "</tbody></table>";

    return ob_get_clean();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
