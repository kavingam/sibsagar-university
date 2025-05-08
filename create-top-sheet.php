<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

// GET variables
$date = $_GET['date'] ?? '12/14/2024';
$time = $_GET['time'] ?? '09:00 AM to 12:00 PM';
$department_id = $_GET['department_id'] ?? null;

require_once 'xyz/Database.php';
require_once 'xyz/bashmodel.php';

$database = Database::getInstance();
$db = $database->getConnection();

$deptObj = new Department($db);
$departments = $deptObj->getAllDepartments();

$attendanceSheet = new AttendanceSheet($db);
$students = $attendanceSheet->getStudentsByExam($date, $time, $department_id);

// print_r($students['student_status']);
echo '<pre>';

// print_r($students);
echo '</pre>';

$departmentNames = [];
foreach ($departments as $dept) {
    $departmentNames[$dept['department_id']] = ucwords(strtolower($dept['department_name']));
}

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
            #printButton, .no-print {
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
            <h6><?= htmlspecialchars($time) ?></h6>
            <h5>Create Top Sheet</h5>
        </div>

        <?php foreach ($groupedStudents as $deptId => $studentsList): 
            $deptName = $departmentNames[$deptId] ?? 'Unknown';
            $subjectCode = strtoupper(substr($deptName, 0, 3));
        ?>
        <div class="meta-info mb-2">
            <div><strong>Department:</strong> <?= htmlspecialchars($deptName) ?></div>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <div class="subject-header"><?= htmlspecialchars(strtoupper($deptName)) ?></div>
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
                    <td><?= htmlspecialchars($subjectCode) ?></td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="<?= $student['roll_no'] ?>"
                                <?= isset($student['student_status']) && $student['student_status'] == 1 ? 'checked' : '' ?>
                                onclick="updateAttendanceStatus(this, '<?= $student['roll_no'] ?>', '<?= $student['course'] ?>', '<?= $student['semester'] ?>')">
                            <label class="form-check-label" for="presentSwitch<?= $student['roll_no'] ?>" id="label<?= $student['roll_no'] ?>">
                                <?= isset($student['student_status']) && $student['student_status'] == 1 ? 'Present' : 'Absent' ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endforeach; ?>

        <div class="text-center mt-5 no-print">
            <button class="btn btn-success" id="update">Generate Top Sheet</button>
        </div>
    </div>

    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // $(document).ready(function() {
        //     // Add click event listener to the checkbox
        //     $('.form-check-input').on('click', function() {
        //         // Get the roll number of the student from the id attribute of the checkbox
        //         var rollNo = $(this).attr('id');
                
        //         // Get the current status text (Present or Absent)
        //         var statusText = $(this).prop('checked') ? 'Present' : 'Absent';
                
        //         // Show an alert with the roll number and current status
        //         alert('Roll No: ' + rollNo + ' is marked as ' + statusText);
        //     });
        // });
    </script>
<script>
// $(document).ready(function() {
//     // Add click event listener to the checkbox
//     $('.form-check-input').on('click', function() {
//         // Get the roll number of the student from the id attribute of the checkbox
//         var rollNo = $(this).attr('id');
        
//         // Get the current status text (Present or Absent)
//         var statusText = $(this).prop('checked') ? 'Present' : 'Absent';
        
//         // Get the student name from the row
//         var row = $(this).closest('tr');
//         var studentName = row.find('td').eq(2).text();  // Student name is in the 3rd column
        
//         // Get the department ID from the PHP variable (passing department ID from PHP)
//         var deptId = '<?= $deptId ?>';  // Use the PHP variable for the department ID

//         // Get date and time from the top section
//         var date = '<?= htmlspecialchars($date) ?>';
//         var time = '<?= htmlspecialchars($time) ?>';

//         // Show an alert with all the details (without department name)
//         alert('Exam Date: ' + date + '\n' +
//               'Exam Time: ' + time + '\n' +
//               'Department ID: ' + deptId + '\n' +
//               'Roll No: ' + rollNo + '\n' +
//               'Student Name: ' + studentName + '\n' +
//               'Status: ' + statusText);
//     });
// });
</script>
<script>
$(document).ready(function() {
    $('.form-check-input').on('click', function() {
        var rollNo = $(this).attr('id');
        var statusText = $(this).prop('checked') ? 1 : 0;  // 1 for Present, 0 for Absent
        var date = '<?= htmlspecialchars($date) ?>';
        var time = '<?= htmlspecialchars($time) ?>';
        var deptId = '<?= $deptId ?>';

        $.ajax({
            url: 'xyz/api/update_attendance.php',  // The PHP file where attendance update happens
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                roll_no: rollNo,
                student_status: statusText,
                date: date,
                time: time,
                department_id: deptId
            }),
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    alert(result.message);
                } else {
                    alert(result.message);
                }
            },
            error: function() {
                alert('An error occurred while updating the attendance.');
            }
        });
    });
});

</script>
<script>
$(document).ready(function() {
    $('#update').on('click', function() {
        // Get values from PHP (embed them in JavaScript)
        var date = '<?= urlencode($date) ?>';
        var time = '<?= urlencode($time) ?>';
        var deptId = '<?= $deptId ?>';

        // Construct the URL with query parameters
        var url = 'print-top-sheet.php?date=' + date + '&time=' + time + '&department_id=' + deptId;

        // Redirect to the URL
        window.location.href = url;
    });
});
</script>



</body>
</html>

