<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../bashmodel.php';
require_once __DIR__ . '/remainder_store.php';
require_once __DIR__ . '/combination_store.php';
require_once __DIR__ . '/seatallocation_store.php';

$roomObj = new Room();
$rooms = $roomObj->getAllRooms();

$remainderStore = new RemainderJSON();
$combinationStore = new combinationJSON();
$seatAllocationStore = new SeatallocationJSON();
$allData = $seatAllocationStore->findAll();

$students = [];
foreach ($allData as $block) {
    $students = array_merge($students, $block['zigzag_students']);
}

$assignRooms  = allocateStudentsToRooms($rooms, count($students));

print_r('<pre>');
// print_r($assignRooms );
// print_r($students);
print_r('</pre>');
$academicYear = date('Y');
$examName = "Semester Exam";
$examDate = date('d-m-Y');
$examTime = "10:00 AM - 1:00 PM";
$examTime24 = "10:00";
$saveData = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hall ticket</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    @media print {
        body {
            margin: 0;
            padding: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 auto;
        }

        .row {
            page-break-inside: avoid;
            margin: 0;
        }

        .col {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 12px;
            width: 166px !important;
            height: 65px !important;
            flex: 0 0 auto !important;
            page-break-inside: avoid;
        }
    }

    /* Optional screen styling */
    .container-fluid {
        padding: 20px;
    }

    .col {
        border: 1px solid #ccc;
        padding: 10px;
        font-size: 12px;
        width: 166px !important;
        height: 65px !important;
        flex: 0 0 auto !important;
        /* Prevents Bootstrap from auto-sizing */
    } 
    .text-label {
        font-size: 6.5px;
        margin-top: -8.2px;
    }
    </style>
</head>

<body>

<?php 
// $student_index = 0;
// $room_index = 0;
// foreach ($assignRooms as $room) {
    
//     $roomNo = $room['room_no'];
//     $roomName = $room['room_name'];
//     $banchOrder = $room['banch_order']; // e.g., 1 means 2 columns per bench
//     $capacity = $room['room_capacity'];
//     $assigned = $room['students_assigned'];
    
//     $xx = $banchOrder * 2;
//     $benchRow = ceil($assigned / $xx);
    
//     $benchCol = $room['banch_order'];

    

//     if ($room_index == 0) {
//         echo '<div class="text-center">';
//         echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>" . htmlspecialchars($room['room_no']) . "</strong> - " . htmlspecialchars($room['room_name']) . "</h4>";
//         echo '</div>';
//         for ($i = 0; $i < $benchRow; $i++) {
//             echo '<div class="container-fluid text-center mt-2" >';
//             echo '<div class="row row-cols-8 g-1 gap-3">';
//             $rowColumns = [];
//             for ($j = 0; $j < $benchCol; $j++) {
//                 $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
//                 $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;

//                 if (!$seat1 && !$seat2) continue;
//                 ob_start();
//                 if ($i % 2 === 0) { 
//                     renderStudentBox($seat1, $saveData, $room, $examDate, $examTime24);
//                     renderStudentBox($seat2, $saveData, $room, $examDate, $examTime24);
//                 } else {
//                     renderStudentBox($seat2, $saveData, $room, $examDate, $examTime24);
//                     renderStudentBox($seat1, $saveData, $room, $examDate, $examTime24);
//                 }
//             }
//             echo '</div>'; 
//             echo '</div>'; 
//         }

//     } else {
//         echo '<div class="text-center page-break mt-5">';
//         echo "<h4 class='mb-3 fs-6 fw-bold'>Room No: <strong>" . htmlspecialchars($room['room_no']) . "</strong> - " . htmlspecialchars($room['room_name']) . "</h4>";
//         echo '</div>';
//         for ($i = 0; $i < $benchRow; $i++) {
//             echo '<div class="container-fluid text-center mt-2">';
//             echo '<div class="row row-cols-8 g-1 gap-3">';
//             $rowColumns = [];
//             for ($j = 0; $j < $benchCol; $j++) {
//                 $seat1 = isset($students[$student_index]) ? $students[$student_index++] : null;
//                 $seat2 = isset($students[$student_index]) ? $students[$student_index++] : null;

//                 if (!$seat1 && !$seat2) continue;
//                 ob_start();
//                 if ($i % 2 === 0) { 
//                     renderStudentBox($seat1, $saveData, $room, $examDate, $examTime24);
//                     renderStudentBox($seat2, $saveData, $room, $examDate, $examTime24);
//                 } else {
//                     renderStudentBox($seat2, $saveData, $room, $examDate, $examTime24);
//                     renderStudentBox($seat1, $saveData, $room, $examDate, $examTime24);
//                 }
//             }
//             echo '</div>';
//             echo '</div>';
//         }

//     }
//     $room_index ++;
//     echo '</div>';
// }

echo '<style>
    .student-box { border: 1px solid #000; padding: 10px; margin-bottom: 5px; min-width: 180px; text-align: left; }
    .student-empty { color: #888; font-style: italic; }
    h2 { margin-top: 40px; }
</style>';

$departmentObj = new Department();
$student_index = 0;
$room_index = 0;

foreach ($assignRooms as $room) {
    $roomNo = $room['room_no'];
    $roomName = $room['room_name'];
    $benchOrder = $room['banch_order']; // number of bench columns
    $capacity = $room['room_capacity'];
    $assigned = $room['students_assigned'];
    
    $seatsPerBench = $benchOrder * 2; // 2 seats per bench column (A, B)
    $benchRow = ceil($assigned / $seatsPerBench);

    // Room header with page break for non-first room
    echo ($room_index > 0) ? '<div class="page-break mt-5">' : '<div>';
    echo '<h2>Room ' . htmlspecialchars($roomNo) . ' - ' . htmlspecialchars($roomName) . '</h2>';

    for ($i = 0; $i < $benchRow; $i++) {
        echo '<div class="container-fluid text-center mt-3">';
        echo '<div class="row row-cols-' . $benchOrder . ' g-2 justify-content-center">';

        for ($j = 0; $j < $benchOrder; $j++) {
            $seatA = isset($students[$student_index]) ? $students[$student_index++] : null;
            $seatB = isset($students[$student_index]) ? $students[$student_index++] : null;

            echo '<div class="col student-box">';
            echo "<strong>Bench [$i,$j]</strong><br>";

            // Seat A
            if ($seatA) {
                echo "Seat A: " . htmlspecialchars($seatA['name']) . " (" . 
                    htmlspecialchars($departmentObj->getDepartmentNameById($seatA['department_id'])) . ")<br>";
            } else {
                echo "Seat A: <span class='student-empty'>Empty</span><br>";
            }

            // Seat B
            if ($seatB) {
                echo "Seat B: " . htmlspecialchars($seatB['name']) . " (" . 
                    htmlspecialchars($departmentObj->getDepartmentNameById($seatB['department_id'])) . ")<br>";
            } else {
                echo "Seat B: <span class='student-empty'>Empty</span><br>";
            }

            echo '</div>';
        }

        echo '</div>'; // close row
        echo '</div>'; // close container
    }

    echo '</div>'; // close room div
    $room_index++;
}


?>
    <!-- <div class="container-fluid text-center">

        <div class="row row-cols-8 g-1 gap-3">    
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>

            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>            
            
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>

            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>

        </div>

        <hr>

        <div class="row row-cols-8 mt-2 g-1 gap-3">    
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>

            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>            
            
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>

            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>
        </div>

    </div>

    <div class="container-fluid text-center page-break mt-5">
        <div class="row row-cols-8 g-1 gap-3">
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>
 
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>
 
            <div class="col p-3">Column</div>
            <div class="col p-3">Column</div>
        </div>
    </div> -->


    <!-- Bootstrap 5.3.3 JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    </script>
</body>

</html>

<?php
function renderStudentBox($seat, $saveData, $room, $examDate, $examTime) {
    $department = new Department();
    if ($seat) {
            
        echo '<div class="col p-3">';
        echo "<p class='text-label'><strong>Roll No:</strong> " . htmlspecialchars($seat['roll_no']) . "</p>";
        echo "<p class='text-label'><strong>Name:</strong> " . htmlspecialchars($seat['name']) . "</p>";
        $deptName = 'NIL';
        if (!empty($seat['department']) && strtoupper(trim($seat['department'])) !== 'NIL') {
            $fetched = $department->getDepartmentName($seat['department']);
            if ($fetched) {
                $deptName = $fetched;
            }
        }
        echo "<p class='text-label'><strong>Department:</strong> " . htmlspecialchars($deptName) . "</p>";

        // echo "<p class='text-label'><strong>Semester:</strong> " . htmlspecialchars($seat['semester']) . "</p>";
        // echo "<p class='text-label'><strong>Course:</strong> " . htmlspecialchars($seat['course']) . "</p>";
        echo "</div>";
        // if (strtoupper(trim($seat['name'])) !== 'NIL') {
            // $attendance = new AttendanceSheet();
            // if ($saveData == 1) {
            //     $attendance->insertAttendance(
            //         $examDate,
            //         $examTime,
            //         $seat['roll_no'],
            //         $seat['name'],
            //         $seat['department'],
            //         $seat['semester'],
            //         $seat['course'],
            //         $room['room_no'],
            //         $room['room_name'],
            //         $room['banch_order'],
            //         0
            //     );
            // }
        // }


    } else {
        echo '<div class="col p-3">No student assigned.</div>';
    }


}

function allocateStudentsToRooms(array $rooms, int $total_students): array
{
    $assigned_rooms = [];

    foreach ($rooms as $room) {
        if ($total_students <= 0)
            break;

        $room_capacity = $room['seat_capacity'] * 2;
        $students_in_room = min($room_capacity, $total_students);

        $assigned_rooms[] = [
            'room_no' => $room['room_no'],
            'room_name' => $room['room_name'],
            'banch_order' => $room['bench_order'],
            'room_capacity' => $room['seat_capacity'],
            'students_assigned' => $students_in_room
        ];

        $total_students -= $students_in_room;
    }

    return $assigned_rooms;
}


?>