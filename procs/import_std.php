<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sibsagar_university";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    $course = $_POST['course'];

    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
        $file = $_FILES['csvFile']['tmp_name'];
        $handle = fopen($file, "r");
        fgetcsv($handle, 1000, ",");
        $duplicates = [];
        $successCount = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $roll_no = $data[0];
            $name = $data[1];
            if (empty($roll_no) || empty($name)) {
                echo json_encode(["status" => "error", "message" => "Invalid data in CSV file. Roll number and name are required."]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO student (roll_no, name, department, semester, course) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiii", $roll_no, $name, $department, $semester, $course);
            if ($stmt->execute()) {
                $successCount++;
            } else {
                if ($stmt->errno == 1062) {
                    $duplicates[] = $roll_no;
                } else {
                    echo json_encode(["status" => "error", "message" => "Error:" . $stmt->error]);
                    exit;
                }
            }

            $stmt->close();
        }
        fclose($handle);
        $response = [
            "status" => "success",
            "message" => "Successfully inserted $successCount records.",
        ];

        echo json_encode($response);
    } else {
        echo json_encode(["status" => "error", "message" => "Error uploading file. Please ensure a valid CSV file is selected."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
?>