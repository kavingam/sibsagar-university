<?php //v.0.0
/*
$servername = "localhost";
$username = "root";
$password = "password";
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
            $reg_no = $data[1];
            $name = $data[2];

            
            // Assign values safely with checks
            // $roll_no = isset($data[0]) ? trim($data[0]) : '';
            // $reg_no  = isset($data[1]) ? trim($data[1]) : '';
            // $name    = isset($data[2]) ? trim($data[2]) : '';


            if (empty($roll_no) || empty($name)) {
                echo json_encode(["status" => "error", "message" => "Invalid data in CSV file. Roll number and name are required."]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO student_info (roll_no, reg_no, name, department, semester, course) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiii", $roll_no, $reg_no, $name, $department, $semester, $course);

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
*/
?>

<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "sibsagar_university";

// Create connection
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

        // Skip header row
        fgetcsv($handle, 1000, ",");

        $duplicates = [];
        $successCount = 0;

        $stmt = $conn->prepare("INSERT INTO student_info (roll_no, reg_no, name, department, semester, course) VALUES (?, ?, ?, ?, ?, ?)");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $roll_no = trim($data[0]);
            $reg_no  = trim($data[1]);
            $name    = trim($data[2]);

            if (empty($roll_no) || empty($name)) {
                continue; // Skip invalid rows
            }

            // Check if the roll number already exists in the database
            $checkStmt = $conn->prepare("SELECT roll_no FROM student_info WHERE roll_no = ?");
            $checkStmt->bind_param("s", $roll_no);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $duplicates[] = $roll_no; // Add duplicate roll number to the list
            } else {
                // Proceed with insertion
                $stmt->bind_param("sssiii", $roll_no, $reg_no, $name, $department, $semester, $course);
                if ($stmt->execute()) {
                    $successCount++;
                } else {
                    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
                    exit;
                }
            }
            $checkStmt->close();
        }

        fclose($handle);

        // Prepare response
        $response = [
            "status" => "success",
            "message" => "Successfully inserted $successCount records.",
        ];

        if (count($duplicates) > 0) {
            $response['duplicates'] = $duplicates;
        }

        echo json_encode($response);

    } else {
        echo json_encode(["status" => "error", "message" => "Error uploading file. Please ensure a valid CSV file is selected."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();

?>
