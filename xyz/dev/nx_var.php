<?php
require_once '../bashmodel.php';
require_once '../seat_allocation/seat_allocation.php';

use SleekDB\Store;
include 'sleekdb_conf.php';
$departmentsStore = new Store("process", $dataDir, $configuration);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $startTime = htmlspecialchars($data['startTime']);
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $tableData = $data['tableData'];

    usort($tableData, function ($a, $b) {
        return $b['totalStudent'] <=> $a['totalStudent'];
    });


    $stdObj = new SeatAllocation();
    $totalStudent =  $stdObj->getTotalStudents($tableData);


    $students = new Student();
    $fetchingSimilarity = [];
    
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'], 
            $data['semester'], 
            $data['course'], 
            $data['totalStudent'] // Ensure total students are passed as range
        );
    
        // Store results
        $fetchingSimilarity[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents // Store retrieved students
        ];
    }

    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    $seatAllocate = findNearestRoom($rooms, ceil($totalStudent / $benchSeat));
    

    echo "<br>Total Examinations Students : ".$totalStudent;
    echo "<br>Seats Per Bench: " . $benchSeat ."<br>";
    
    // echo "<pre>";
    //print_r($tableData); // Department Details With Desending to Assending Order
    echo "<br>";
    // print_r($fetchingSimilarity); // Selected Data Fetching And Retrive Details
    // echo "<br>";
    // print_r($fetchingSimilarity);
    // $departmentsStore = new Store("process", $dataDir, $configuration);
    try {
        // $departmentsStore->insertMany($fetchingSimilarity);
        // ✅ Check if department already exists (ignoring `students` subarray)
        foreach ($fetchingSimilarity as $dept) {
        $existingData = $departmentsStore->findBy([
            ["department", "=", $dept["department"]],
            ["semester", "=", $dept["semester"]],
            ["course", "=", $dept["course"]]
        ]);

        if (empty($existingData)) {
            // ✅ Insert only if no matching department exists
            $departmentsStore->insertMany($dept);
            echo "✅ Department {$dept['department']} inserted successfully!<br>";
        } else {
            echo "⚠️ Department {$dept['department']} already exists. Skipping...<br>";
        }
    }
        // echo "Connection successful!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    
}
?>


<?php
function findNearestRoom($rooms, $targetCapacity) {
    usort($rooms, function($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    $selectedRooms = [];
    $totalSeats = 0;

    foreach ($rooms as $room) {
        $selectedRooms[] = $room;
        $totalSeats += $room['seat_capacity'];

        if ($totalSeats >= $targetCapacity) {
            return [
                'room' => $selectedRooms,
                'adjustment' => (count($selectedRooms) == 1) ? "Single Room Assigned" : "Merged Multiple Rooms"
            ];
        }
    }
    return [
        'room' => [],
        'adjustment' => "No Suitable Room Found (Minimum seat capacity is " . $rooms[0]['seat_capacity'] . ")"
    ];
}?>