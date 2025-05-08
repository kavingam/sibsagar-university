<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "sibsagar_university";

// DB Connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['department_id'], $_POST['semester'], $_POST['course']) ||
        !is_numeric($_POST['department_id']) || !is_numeric($_POST['semester']) || !is_numeric($_POST['course'])
    ) {
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
        exit;
    }

    $department_id = (int)$_POST['department_id'];
    $semester = (int)$_POST['semester'];
    $course = (int)$_POST['course']; // now course is integer

    // Get department name from DB
    $deptStmt = $conn->prepare("SELECT department_name FROM departments WHERE department_id = ?");
    $deptStmt->bind_param("i", $department_id);
    $deptStmt->execute();
    $deptStmt->bind_result($department_name);
    $deptStmt->fetch();
    $deptStmt->close();

    if (empty($department_name)) {
        echo json_encode(["status" => "error", "message" => "Invalid department ID."]);
        exit;
    }

    if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== 0) {
        echo json_encode(["status" => "error", "message" => "Error uploading file. Please ensure a valid CSV file is selected."]);
        exit;
    }

    $file = $_FILES['csvFile']['tmp_name'];
    $mimeType = mime_content_type($file);
    if (!in_array($mimeType, ['text/plain', 'text/csv', 'application/vnd.ms-excel'])) {
        echo json_encode(["status" => "error", "message" => "Invalid file type."]);
        exit;
    }

    $handle = fopen($file, "r");
    if (!$handle) {
        echo json_encode(["status" => "error", "message" => "Failed to open the uploaded file."]);
        exit;
    }

    fgetcsv($handle, 1000, ","); // Skip header
    $duplicates = [];
    $successCount = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) < 2) continue;

        $subject = trim($data[0]);
        $subject_code = trim($data[1]);

        if (empty($subject) || empty($subject_code)) continue;

        // Check for duplicate (based on department_id, semester, course, and subject_code)
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM subject_info WHERE department_id = ? AND course = ? AND semester = ? AND subject_code = ?");
        $checkStmt->bind_param("iiis", $department_id, $course, $semester, $subject_code);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $duplicates[] = ["subject" => $subject, "subject_code" => $subject_code, "course" => $course, "semester" => $semester];
            continue;
        }

        // Insert subject
        $stmt = $conn->prepare("INSERT INTO subject_info (department_id, department_name, course, semester, subject, subject_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiss", $department_id, $department_name, $course, $semester, $subject, $subject_code);

        if ($stmt->execute()) {
            $successCount++;
        } else {
            echo json_encode(["status" => "error", "message" => "Insert error: " . $stmt->error]);
            $stmt->close();
            fclose($handle);
            exit;
        }

        $stmt->close();
    }

    fclose($handle);

    $response = [
        "status" => "success",
        "message" => "Successfully inserted $successCount record(s)."
    ];

    if (!empty($duplicates)) {
        $response["status"] = "warning";
        $response["message"] .= " Skipped " . count($duplicates) . " duplicate(s).";
        $response["duplicates"] = $duplicates;
    }

    echo json_encode($response);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>
