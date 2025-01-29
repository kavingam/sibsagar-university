<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sibsagar_university";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$department = $_POST['department'];
$semester = $_POST['semester'];
$course = $_POST['course'];

$sql = "SELECT roll_no, name, department, semester, course FROM student WHERE department = ? AND semester = ? AND course = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $department, $semester, $course);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($students);
?>
