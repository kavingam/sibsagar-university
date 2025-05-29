<?php
header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// get_departments.php
include('../db/pdo_connect.php');

header('Content-Type: application/json');

$sql = 'SELECT department_id, department_name FROM departments';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['departments' => $departments]);
