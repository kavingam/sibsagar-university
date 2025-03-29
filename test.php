<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zigzag Seating - Left & Right Box Design</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        .room {
            border: 2px solid #333;
            margin: 20px auto;
            padding: 10px;
            width: fit-content;
            display: inline-block;
            background: #f5f5f5;
            border-radius: 8px;
        }
        .room-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #333;
            color: white;
            padding: 5px;
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
        }
        .seat {
            flex: 1;
            padding: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .left-seat {
            background: #ffcccc;
            border-right: 2px solid #555;
        }
        .right-seat {
            background: #cce5ff;
        }
        .even-row {
            flex-direction: row-reverse;
            background: #ffd6d6;
        }
    </style>
</head>
<body>

    <h2>Zigzag Seating (8 Rows x 3 Columns) - Left & Right Box Design</h2>

    <?php
    // Configuration
    $rows = 8; // Fixed number of rows per room
    $benchesPerRow = 3; // Fixed number of benches per row
    $studentsPerRoom = $rows * $benchesPerRow * 2; // Total capacity per room
    $totalStudents = 180; // Total number of students
    $departments = ["CS", "Math", "Physics", "Chem", "Bio", "English", "History", "Economics"];
    $classesPerDept = 2; // Classes per department
    $studentsPerClass = 5; // Students per class
    $seatNumber = 1;
    $roomNumber = 1;

    while ($seatNumber <= $totalStudents) {
        echo "<div class='room'>";
        echo "<div class='room-title'>Room $roomNumber</div>";
        echo "<div class='container'>";

        for ($r = 1; $r <= $rows && $seatNumber <= $totalStudents; $r++) {
            echo '<div class="row ' . ($r % 2 == 0 ? 'even-row' : '') . '">';
            
            $rowBenches = [];
            for ($b = 1; $b <= $benchesPerRow && $seatNumber <= $totalStudents; $b++) {
                $seatInfo1 = $seatInfo2 = "Empty";

                // Assign first student (Left Seat)
                if ($seatNumber <= $totalStudents) {
                    $deptIndex1 = ($seatNumber - 1) % count($departments);
                    $classNumber1 = (($seatNumber - 1) / $studentsPerClass) % $classesPerDept + 1;
                    $studentNumber1 = (($seatNumber - 1) % $studentsPerClass) + 1;

                    $seatInfo1 = $departments[$deptIndex1] . "<br>Class " . $classNumber1 . "<br>Seat " . $studentNumber1;
                    $seatNumber++;
                }

                // Assign second student (Right Seat, Different Department)
                if ($seatNumber <= $totalStudents) {
                    do {
                        $deptIndex2 = ($seatNumber - 1) % count($departments);
                    } while ($deptIndex2 == $deptIndex1); // Ensure different department

                    $classNumber2 = (($seatNumber - 1) / $studentsPerClass) % $classesPerDept + 1;
                    $studentNumber2 = (($seatNumber - 1) % $studentsPerClass) + 1;

                    $seatInfo2 = $departments[$deptIndex2] . "<br>Class " . $classNumber2 . "<br>Seat " . $studentNumber2;
                    $seatNumber++;
                }

                $rowBenches[] = "<div class='bench'>
                                    <div class='seat left-seat'>$seatInfo1</div>
                                    <div class='seat right-seat'>$seatInfo2</div>
                                 </div>";
            }

            if ($r % 2 == 0) {
                $rowBenches = array_reverse($rowBenches);
            }

            echo implode("", $rowBenches);
            echo '</div>';
        }

        echo "</div></div>";
        $roomNumber++;
    }
    ?>

</body>
</html>
<!-- Seat(R, C) = (D - 1) * Cl * S + (R - 1) * C + (C - 1) * Cl + (Cl - 1) * S + S
 -->