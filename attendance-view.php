<?php
// Sample GET variables (replace with actual values or keep as fallback)
$date = $_GET['date'] ?? '12/14/2024';
$time = $_GET['time'] ?? '09:00 AM to 12:00 PM';
$room_no = $_GET['room_no'] ?? 'PG-3';

// Load models
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';
// require_once 'xyz/Department.php';

$database = Database::getInstance();
$db = $database->getConnection();

$deptObj = new Department($db);
$departments = $deptObj->getAllDepartments();

$attendanceSheet = new AttendanceSheet($db);
$students = $attendanceSheet->getStudentsByRoom($date, $time, $room_no);

// Map departments
$departmentNames = [];
foreach ($departments as $dept) {
    $departmentNames[$dept['department_id']] = $dept['department_name'];
}

// Group students by department
$groupedStudents = [];
foreach ($students as $student) {
    $groupedStudents[$student['department']][] = $student;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 12pt;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            #printButton {
                display: none;
            }

            .table th {
                background-color: #d3d3d3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h5 {
            margin: 5px 0;
        }

        .meta-info {
            font-weight: bold;
            font-size: 14px;
        }

        .summary-table td {
            height: 40px;
        }

        .signature-box {
            margin-top: 20px;
            float: right;
            width: 250px;
            text-align: center;
        }

        .subject-header {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
        @media print {
    .no-print {
        display: none;
    }

    .signature-box {
        margin-top: 80px;
        margin-bottom: 40px;
    }
}

.signature-box {
    margin-top: 60px;
    text-align: right;
    padding-right: 50px;
}

    </style>
</head>
<body class="p-4 vh-100">

    <div class="header">
        <h5>Sibsagar University Exam 2024</h5>
        <h6><?= htmlspecialchars($date) ?></h6>
        <h6><?= htmlspecialchars( date("h:i A", strtotime($time))) ?></h6>
        <h5>Attendance Sheet</h5>
    </div>

    <?php foreach ($groupedStudents as $deptId => $studentsList): 
        $deptName = $departmentNames[$deptId] ?? 'Unknown';
        $subjectCode = strtoupper(substr($deptName, 0, 3)); // Simplified subject code
    ?>
    <div class="meta-info mb-2">
        <div><strong>ROOM:</strong> <?= htmlspecialchars($room_no) ?></div>
    </div>

    <div class="d-flex justify-content-between mb-2">
        <!-- <div class="fw-bold">Roll No. | Reg. No.</div> -->
        <div class="subject-header"> <?= strtoupper($deptName) ?></div>
        <!-- <div class="subject-header"><?= $subjectCode ?> <?= strtoupper($deptName) ?></div> -->

    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Roll No</th>
                <th>Reg. No</th>
                <th>Student Name</th>
                <th>Paper</th>
                <th>Signature</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($studentsList as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['roll_no']) ?></td>
                <td><?= htmlspecialchars($student['reg_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= $subjectCode ?></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>

<!-- Subject-wise Summary -->
<h5 class="mt-5">Summary</h5>
<table class="table table-bordered summary-table w-50">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Total Allotted</th>
            <th>Total Absent</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $subjectCounter = 1;
        foreach ($groupedStudents as $deptId => $studentsList):
            $deptName = $departmentNames[$deptId] ?? 'Unknown';
            $subjectCode = strtoupper(substr($deptName, 0, 3)); // Customize as needed
            $totalAllotted = count($studentsList);
        ?>
        <tr>
            <td>Subject <?= $subjectCounter ?>: <?= htmlspecialchars($deptName) ?></td>
            <td><?= $totalAllotted ?></td>
            <td></td> <!-- Leave blank for manual entry -->
        </tr>
        <?php $subjectCounter++; endforeach; ?>
    </tbody>
</table>


<!-- Signature Area -->
<div class="signature-box mt-5 mb-2 text-end">
    <strong>Sign. of Invigilator(s)</strong>
</div>

<!-- Print Button at the bottom -->
<div class="text-center mt-5 no-print">
    <button onclick="window.print()" class="btn btn-success">Print</button>
</div>

</body>
</html>
