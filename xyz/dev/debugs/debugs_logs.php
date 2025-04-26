<?php
if (!file_exists($bashmodelPath)) {
    die("Error: bashmodel.php not found at: $bashmodelPath");
}
require_once $bashmodelPath;

if (!file_exists($seatAllocationPath)) {
    die("Error: seat_allocation.php not found at: $seatAllocationPath");
}
require_once $seatAllocationPath;


if (!file_exists($remainderPath)) {
    die("Error: remainder_store.php not found at: remainder_store.php");
}
require_once $remainderPath;

if (!file_exists($combinationPath)) {
    die("Error: combination_store.php not found at: combination_store.php");
}
require_once $combinationPath;

if (!file_exists($allocationPath)) {
    die("Error: seatallocation_store.php not found at: $seatAllocationStorePath");
}
require_once $allocationPath;

?>