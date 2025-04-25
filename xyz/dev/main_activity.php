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
    $totalDept = count($tableData);


    $roomObj = new Room();
    $rooms = $roomObj->getAllRooms();

    echo '<pre>';
    // print_r($fetchStudents);
    // print_r($rooms);
    // echo $totalStudent;
    // echo $totalDept;
    echo '</pre>';

    deleteJsonFiles($remove_cache_x);
    deleteJsonFiles($remove_cache_y);

    $remainderStore = new RemainderJSON();
    $remainderStore = new NewRemainderJSON($remainderStore);

    $combinationStore = new CombinationJSON();
    $combinationStore = new NewCombinationJSON($combinationStore);

  

    if (isEven($totalDept)) {
        echo '<pre>';
        // $studentSeatCounts = computeSeatCounts($fetchStudents);
        // print_r($studentSeatCounts);
        // print_r(pairSubtractRemainder($fetchStudents));
        print_r(getSimilarPairs($fetchStudents));

        echo '</pre>';

    } else {
        // array_pop($fetchStudents);
        // $allButLast = array_slice($fetchStudents, 0, -1);
        // $lastDept = end($fetchStudents);
        // print_r($lastDept);
        // print_r($fetchStudents);
        // print_r($allButLast);
        echo '<pre>';
        // print_r(pairSubtractRemainder($fetchStudents));
        echo '</pre>';
    }
}


?>

<?php
/** Testing Ok
 * Check if a number is even or odd.
 *
 * @param int $num The number to check.
 * @return int 1 if the number is even, 0 if it’s odd.
 */
function isEven(int $num): int {
    return ($num % 2 === 0) ? 1 : 0;
}
?>

<?php
/**
 * Computes student seat counts by pairing every two elements:
 * - For each pair, uses the second element’s totalStudent * 2
 * - If a lone element remains (odd length), uses its totalStudent
 *
 * @param array $blocks Array of associative arrays, each with a 'totalStudent' key.
 * @return int[]        Array of computed seat counts.
 */
function computeSeatCounts(array $blocks): array {
    $seatCounts = [];

    $n = count($blocks);
    for ($i = 0; $i < $n; $i += 2) {
        // If there is a next (paired) block...
        if (isset($blocks[$i + 1])) {
            $seatCounts[] = $blocks[$i + 1]['totalStudent'] * 2;
        } else {
            // No pair—take the last block’s own total
            $seatCounts[] = $blocks[$i]['totalStudent'];
        }
    }

    return $seatCounts;
}


/** Testing Ok
 * Pair up department blocks two‑by‑two, subtract their 'totalStudent' values,
 * and for each pair return a new block with:
 *   - department/semester/course (from the first block)
 *   - totalStudent (the subtraction result)
 *   - students        (the first block’s students, truncated to the new total)
 *
 * Any unpaired (odd) block is returned in 'remainder' exactly as‑is.
 *
 * @param array $items Indexed array of department‑blocks, each with:
 *                     department, semester, course, totalStudent, students (array)
 * @return array{pairs: array, remainder: array}
 */
function pairSubtractRemainder(array $items): array {
    $pairs     = [];
    $remainder = [];
    $n         = count($items);

    for ($i = 0; $i + 1 < $n; $i += 2) {
        $first  = $items[$i];
        $second = $items[$i + 1];

        // Ensure both have totalStudent and students:
        if (isset($first['totalStudent'], $second['totalStudent'], $first['students']) 
            && is_array($first['students'])) {
            
            // Compute new total
            $newTotal = $first['totalStudent'] - $second['totalStudent'];
            $newTotal = max(0, $newTotal); // avoid negative

            // Truncate the students list to the new total count
            $newStudents = array_slice($first['students'], -$newTotal);

            // Build the resulting block
            $pairs[] = [
                'department'   => $first['department'],
                'semester'     => $first['semester'],
                'course'       => $first['course'],
                'totalStudent' => $newTotal,
                'students'     => $newStudents,
            ];
        }
    }

    // Odd one out?
    if ($n % 2 !== 0) {
        $remainder[] = $items[$n - 1];
    }

    return [
        'pairs'     => $pairs,
        'remainder' => $remainder,
    ];
}
?>
<?php
/**
 * Get similar pairs from the fetching similarity array.
 *
 * @param array $fetchingSimilarity The array containing similar students.
 * @return array An array of pairs of similar students.
 */
function getSimilarPairs(array $fetchingSimilarity): array {
    $result = [];

    for ($i = 0; $i < count($fetchingSimilarity); $i += 2) {
        $first = $fetchingSimilarity[$i];
        $second = $fetchingSimilarity[$i + 1] ?? null; // Avoid error if odd count

        // Only add pair if both elements exist
        if ($second !== null) {
            $result[] = [$first, $second];
        }
    }

    return $result;
}

?>