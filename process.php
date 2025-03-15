<?php
include "generate.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $startTime = htmlspecialchars($data['startTime']);
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $tableData = $data['tableData'];

    switch ($benchSeat) {
        case 1:
            $benchPlan = new BenchSeatPlan($startTime, $benchSeat, $tableData);
            echo $benchPlan->getSingleBench();
            // $benchPlan-> printTableData();
            break;
        case 2:
            echo "Two seat bench";
            break;
        case 3:
            echo "Three seat bench";
            break;
        case 4:
            echo "Four seat bench";
            break;
        case 5:
            echo "Five seat bench";
            break;
        case 6:
            echo "Six seat bench";
            break;
        case 7:
            echo "Seven seat bench";
            break;
        case 8:
            echo "Eight seat bench";
            break;
        case 9:
            echo "Nine seat bench";
            break;
        case 10:
            echo "Ten seat bench";
            break;
        default:
            echo "Invalid bench seat value";
            break;
    }
}
?>