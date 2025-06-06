<?php
$host = 'localhost';
$dbname = 'sibsagar_university';
$username = 'root';
$password = 'password';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST variables
    $tableData = isset($_POST['tableData']) ? json_decode($_POST['tableData'], true) : [];
    $startTime = $_POST['startTime'] ?? '';
    $startDate = $_POST['startDate'] ?? '';
    $benchSeat = $_POST['benchSeat'] ?? null;
    $selectedExam = $_POST['selectedExam'] ?? '';
    $enteredExamName = $_POST['enteredExamName'] ?? '';

    if (!$startTime || !$startDate || !$selectedExam) {
        echo "❌ Missing required data.";
        exit;
    }

    // Determine final exam name (if 'Other' use enteredExamName)
    $examName = ($selectedExam === 'Other' && !empty($enteredExamName)) ? $enteredExamName : $selectedExam;

    // Check if entry already exists with same startTime, startDate, examName
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM seating_plan WHERE start_time = ? AND start_date = ? AND exam_name = ?");
    $checkStmt->execute([$startTime, $startDate, $examName]);
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        echo "❌ Duplicate entry: Exam already saved for this date and time.";
        exit;
    }

    // Insert data into seating_plan table
    $insertStmt = $pdo->prepare("INSERT INTO seating_plan (start_time, start_date, exam_name, bench_seat, data_json) VALUES (?, ?, ?, ?, ?)");
    $insertStmt->execute([
        $startTime,
        $startDate,
        $examName,
        $benchSeat,
        json_encode($tableData)
    ]);

    echo "✅ Successfully saved!";
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
