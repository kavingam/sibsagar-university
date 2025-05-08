<?php 
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

// GET variables
$date = $_GET['date'] ?? '12/14/2024';
$time = $_GET['time'] ?? '09:00 AM to 12:00 PM';
$room_no = $_GET['room_no'] ?? 'PG-3';

// Load models
require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

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

            #printButton,
            .no-print {
                display: none;
            }

            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 15mm;
            }

            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto;
            }

            .table th {
                background-color: #d3d3d3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .form-check-input {
                pointer-events: none;
            }

            .form-check {
                transform: scale(0.85);
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

        .subject-header {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <div class="header">
            <h5>Sibsagar University Exam 2024</h5>
            <h6><?= htmlspecialchars($date) ?></h6>
            <h6><?= htmlspecialchars(date("h:i A", strtotime($time))) ?></h6>
            <h5>Create Top Sheet</h5>
        </div>

        <?php foreach ($groupedStudents as $deptId => $studentsList): 
        $deptName = $departmentNames[$deptId] ?? 'Unknown';
        $subjectCode = strtoupper(substr($deptName, 0, 3));
         ?>
        <div class="meta-info mb-2">
            <div><strong>ROOM:</strong> <?= htmlspecialchars($room_no) ?></div>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <div class="subject-header"><?= strtoupper($deptName) ?></div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Roll No</th>
                    <th>Reg. No</th>
                    <th>Student Name</th>
                    <th>Paper</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentsList as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['roll_no']) ?></td>
                    <td><?= htmlspecialchars($student['reg_no'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= $subjectCode ?></td>
                    <td>
                        <div class="form-check form-switch">
                            <!-- No checked attribute, meaning default is 'Absent' -->
                            <input class="form-check-input" type="checkbox"
                                id="presentSwitch<?= $student['roll_no'] ?>">
                            <label class="form-check-label" for="presentSwitch<?= $student['roll_no'] ?>"
                                id="label<?= $student['roll_no'] ?>">Absent</label>
                        </div>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endforeach; ?>
        <!-- Print Button -->
        <div class="text-center mt-5 no-print">
            <button class="btn btn-success">Generate Top Sheet</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-check-input').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const labelId = 'label' + this.id.replace('presentSwitch', '');
                    const label = document.getElementById(labelId);
                    // If checked, it's 'Present'; otherwise, it's 'Absent'
                    label.textContent = this.checked ? 'Present' : 'Absent';
                });
            });
        });
    </script>

</body>

</html>