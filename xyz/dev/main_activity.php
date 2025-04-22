<?php 
/*
    Reuseability Functions
*/
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
<?php 

/*
    Debug Version 0.1
*/

error_reporting(E_ALL);
ini_set('display_errors',1);


$bashmodelPath = __DIR__ . '/../bashmodel.php';
$seatAllocationPath = __DIR__ . '/../seat_allocation/seat_allocation.php';

$remainderPath = __DIR__ . '/remainder_store.php';
$combinationPath = __DIR__ . '/combination_store.php';



$remove_cache_x = __DIR__ . '/json_database/combination_json/data';
$remove_cache_y = __DIR__ . '/json_database/remainder_json/data';

if (file_exists('debugs_logs.php')) {
    include_once 'debugs_logs.php';
} else {
    echo "Error: debugs_logs.php not found<br>";
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

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

    $students = new Student();
    $fetchStudents = [];
    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['semester'],
            $data['course'],
            $data['totalStudent']
        );

        $fetchStudents[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    $stdObj = new SeatAllocation();
    $totalStudent = $stdObj->getTotalStudents($tableData);

    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    echo '<pre>';
    print_r($fetchStudents);
    // print_r($rooms);
    // echo $totalStudent;
    echo '</pre>';

    deleteJsonFiles($remove_cache_x);
    deleteJsonFiles($remove_cache_y);

    $remainderStore = new RemainderJSON();
    $remainderStore = new NewRemainderJSON($remainderStore);

    $combinationStore = new CombinationJSON();
    $combinationStore = new NewCombinationJSON($combinationStore);
}


?>