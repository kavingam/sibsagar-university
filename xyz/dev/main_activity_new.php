<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once(__DIR__ . '/../bashmodel.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
require_once(__DIR__ . '/../bashmodel.php');

    $tableData = json_decode($_POST['tableData'], true);
    $startTime = $_POST['startTime'];
    $benchSeat = $_POST['benchSeat'];
    $selectedExam = $_POST['selectedExam'];
    $enteredExamName = $_POST['enteredExamName'];
    $startDate = $_POST['startDate'];
    $save = $_POST['save'] ?? 0;

    usort($tableData, fn($a, $b) => $b['totalStudent'] <=> $a['totalStudent']);

    $students = new Student();
    $rooms = new Room();

    $fetchStudents = [];

    // foreach ($tableData as $data) {
    //     $similarStudents = $students->findSimilarStudents(
    //         $data['department'],
    //         $data['course'],
    //         $data['semester']
    //     );

    //     $fetchStudents[] = [
    //         'department' => $data['department'],
    //         'semester' => $data['semester'],
    //         'course' => $data['course'],
    //         'subject' => $data['subject'],
    //         'totalStudent' => $data['totalStudent'],
    //         'students' => $similarStudents
    //     ];
    // }

    foreach ($tableData as $data) {
        $department = trim($data['department']);
        $course = trim($data['course']);
        $semester = trim($data['semester']);
    
        $similarStudents = $students->findSimilarStudents($department, $course, $semester);
    
        $fetchStudents[] = [
            'department' => $department,
            'semester' => $semester,
            'course' => $course,
            'subject' => $data['subject'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }
    
    // Process as needed
    echo '<pre>';
    print_r($tableData);
    print_r($fetchStudents);
}
?>