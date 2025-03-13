<?php
include "generate.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $startTime = htmlspecialchars($data['startTime']);
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $tableData = $data['tableData'];

    switch ($benchSeat) {
        case 1:
            $benchPlan = new BenchSeatPlan($startTime, $benchSeat, $tableData);
            echo $benchPlan->getSingleBench();
            // $benchPlan-> printTableData();
            break;
        case 2:
            echo "Two seat bench";
            break;
        case 3:
            echo "Three seat bench";
            break;
        case 4:
            echo "Four seat bench";
            break;
        case 5:
            echo "Five seat bench";
            break;
        case 6:
            echo "Six seat bench";
            break;
        case 7:
            echo "Seven seat bench";
            break;
        case 8:
            echo "Eight seat bench";
            break;
        case 9:
            echo "Nine seat bench";
            break;
        case 10:
            echo "Ten seat bench";
            break;
        default:
            echo "Invalid bench seat value";
            break;
    }
}
    // echo "<div class=\"container-fluid p-3\">";
    // echo "<p class=\"fs-5 fw-bold text-center\">sibsagar university</p>";
    // echo "<p>Exam Start Time: $startTime</p>";
    // echo "<p>Bench Seat: $benchSeat</p>";
    // echo "</div";
    // echo "<hr>";
    // echo "<ul class='list-group'>";
    // foreach ($tableData as $row) {
    //     echo "<li class='list-group-item'>";
    //     echo "<strong>Department:</strong> " . htmlspecialchars($row['department']) . " | ";
    //     echo "<strong>Course:</strong> " . htmlspecialchars($row['course']) . " | ";
    //     echo "<strong>Semester:</strong> " . htmlspecialchars($row['semester']);
    //     echo "</li>";
    // }
    // echo "</ul>";
/*
// Create an instance of Room class
$roomObj = new Room();
$allRooms = $roomObj->getAllRooms();

// Output Room Details
echo "<h2>All Room Details</h2>";
foreach ($allRooms as $room) {
    echo "Room No: " . htmlspecialchars($room['room_no']) . "<br>";
    echo "Room Name: " . htmlspecialchars($room['room_name']) . "<br>";
    echo "Bench Order: " . htmlspecialchars($room['bench_order']) . "<br>";
    echo "Seat Capacity: " . htmlspecialchars($room['seat_capacity']) . "<br><br>";
}
// Create an instance of the Department class
$departmentObj = new Department();

// Fetch all departments
$allDepartments = $departmentObj->getAllDepartments();

// Output Department Details
echo "<h2>All Department Details</h2>";
foreach ($allDepartments as $dept) {
    echo "Department ID: " . htmlspecialchars($dept['department_id']) . "<br>";
    echo "Department Name: " . htmlspecialchars($dept['department_name']) . "<br><br>";
    // echo "Department"
}

// Fetch specific department by ID (Example: ID = 'D101')
$deptId = '1';
$departmentDetails = $departmentObj->getDepartmentById($deptId);

if ($departmentDetails) {
    echo "<h2>Department Details for ID: $deptId</h2>";
    echo "Department Name: " . htmlspecialchars($departmentDetails['department_name']) . "<br>";
} else {
    echo "<h2>No Department Found for ID: $deptId</h2>";
}

}



// Create an instance of the Student class
$studentObj = new Student();

// Fetch all students
$allStudents = $studentObj->getAllStudents();

// Output all student details
echo "<h2>All Student Details</h2>";
foreach ($allStudents as $student) {
    echo "Roll No: " . htmlspecialchars($student['roll_no']) . "<br>";
    echo "Name: " . htmlspecialchars($student['name']) . "<br>";
    echo "Department: " . htmlspecialchars($student['department']) . "<br>";
    echo "Semester: " . htmlspecialchars($student['semester']) . "<br>";
    echo "Course: " . htmlspecialchars($student['course']) . "<br><br>";
}

// Fetch students by criteria (Example: Department = 1, Semester = 5, Course = 101)
$studentsByCriteria = $studentObj->getStudentsByCriteria(1, 5, 101);

echo "<h2>Students in Department 1, Semester 5, Course 101</h2>";
foreach ($studentsByCriteria as $student) {
    echo "Roll No: " . htmlspecialchars($student['roll_no']) . "<br>";
    echo "Name: " . htmlspecialchars($student['name']) . "<br><br>";
}

// Fetch a specific student by Roll No (Example: 'S101')
$rollNo = 'S101';
$studentDetails = $studentObj->getStudentByRollNo($rollNo);

if ($studentDetails) {
    echo "<h2>Details for Roll No: $rollNo</h2>";
    echo "Name: " . htmlspecialchars($studentDetails['name']) . "<br>";
    echo "Department: " . htmlspecialchars($studentDetails['department']) . "<br>";
    echo "Semester: " . htmlspecialchars($studentDetails['semester']) . "<br>";
    echo "Course: " . htmlspecialchars($studentDetails['course']) . "<br>";
} else {
    echo "<h2>No Student Found for Roll No: $rollNo</h2>";
}
*/

?>