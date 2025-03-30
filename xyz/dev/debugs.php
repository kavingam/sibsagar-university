<?php 
if (!file_exists($bashmodelPath)) {
    die("Error: bashmodel.php not found at: $bashmodelPath");
}
require_once $bashmodelPath;

if (!file_exists($seatAllocationPath)) {
    die("Error: seat_allocation.php not found at: $seatAllocationPath");
}
require_once $seatAllocationPath;

if (!file_exists($sleekdbPath)) {
    die("Error: sleekdb.php not found at: $sleekdbPath");
}
require_once $sleekdbPath;

if (!file_exists($sleekdbxPath)) {
    die("Error: sleekdbx.php not found at: $sleekdbPathx");
}
require_once $sleekdbxPath;

if(!file_exists($layout_path)) {
    die("Error: layout_xyz.php not found at: $layout_path");
}
require_once $layout_path;
?>