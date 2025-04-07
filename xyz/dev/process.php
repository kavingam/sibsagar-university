<?php
$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';
$sleekdbPath = __DIR__ . '/sleekdb.php';
$sleekdbxPath = __DIR__ . '/sleekdbx.php';
$filePath = __DIR__ . '/rooms.json';
require __DIR__ . '/debugs.php';

$RemoveJsonPathxx = __DIR__ . '/database/departments/data/';
$RemoveJsonPathxy = __DIR__ . '/database/seatAllocationList/data/';
$TestingJsonPathyx = __DIR__ . '/database/test_connection/data/';

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

    // students info
    $students = new Student();
    $fetchingSimilarity = [];
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['semester'],
            $data['course'],
            $data['totalStudent']
        );

        // Store results
        $fetchingSimilarity[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    // total students
    $stdObj = new SeatAllocation();
    $totalStudent = $stdObj->getTotalStudents($tableData);

    // rooms
    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    // $fetchingSimilarity;
    // $totalStudent;
    // $rooms;

    deleteJsonFiles($RemoveJsonPathxx);
    deleteJsonFiles($RemoveJsonPathxy);

    echo '<pre>';
    print_r($fetchingSimilarity);

    // calculate total examination students
    try {
    //     $studentSeatCounts = [];
    //     for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
    //         if (isset($fetchingSimilarity[$i + 1])) {
    //             $studentSeatCounts[] = $fetchingSimilarity[$i + 1]['totalStudent'] * 2;
    //         } else {
    //             $studentSeatCounts[] = $fetchingSimilarity[$i]['totalStudent'];
    //         }
    //     }
    //     echo '<pre/>';
    //     print_r($studentSeatCounts);
        // Array
        // (
        //     [0] => 60
        //     [1] => 25
        // )


    } catch (Exception $e) {
        echo 'Fucking Error: ' . $e->getMessage();
    }

    

    // calculate total Student final remainder 
    try {
        $departmentsStore = new DepartmentStore();
        $departmentsStore = new AdvancedDepartmentStore($departmentsStore);

        $departmentsStore->bulkInsert($fetchingSimilarity);
        $getTotalDepartment = $departmentsStore->findAll();
        $getTotalDept = count($getTotalDepartment);

        for ($i = 0; $i < $getTotalDept; $i++) {

            $retriveTotalDept = $departmentsStore->findAll();

            if (count($retriveTotalDept) < 2) {
                break;
            }

            usort($retriveTotalDept, function ($a, $b) {
                return $b['totalStudent'] - $a['totalStudent'];
            });

            $firstDump = $retriveTotalDept[0];
            $secondDump = $retriveTotalDept[1];

            $stdToDump = min(count($secondDump['students']), count($firstDump['students']));
            $stdToVar = array_slice($firstDump['students'], $stdToDump);

            $varRemainder = [
                [
                    'department' => $firstDump['department'],
                    'semester' => $firstDump['semester'],
                    'course' => $firstDump['course'],
                    'totalStudent' => count($stdToVar),
                    'students' => $stdToVar
                ]
            ];
            if (isset($firstDump['_id']) && isset($secondDump['_id'])) {
                $deleted1 = $departmentsStore->deleteById($firstDump['_id']);
                $deleted2 = $departmentsStore->deleteById($secondDump['_id']);
                $departmentsStore->bulkInsert($varRemainder);
            }

        }
    } catch (Exception $e) {
        echo 'Error:  Remainder Seat Allote ' . $e->getMessage();
    }
}
?>

<?php
function deleteJsonFiles($directory)
{
    if (!is_dir($directory)) {
        die("Error: Directory '$directory' does not exist.<br>");
    }
    $files = glob($directory . '*.json');
    if (empty($files)) {
        return;
    }
    foreach ($files as $file) {
        if (unlink($file)) {
            // echo "Deleted: $file<br/>";
        } else {
            // echo "Failed to delete: $file<br/>";
        }
    }
}
?>