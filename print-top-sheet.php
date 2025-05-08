<?php
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$department_id = $_GET['department_id'] ?? '';

// You can now query the DB to generate the top sheet
// Example:
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM attendance_sheet WHERE date = :date AND time = :time AND department = :department_id");
$stmt->execute([
    ':date' => $date,
    ':time' => $time,
    ':department_id' => $department_id
]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($students);
echo '</pre>;'
// Display as needed
?>
