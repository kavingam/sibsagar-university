<?php 
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

require_once 'db/pdo_connect.php';
// Initialize an array to hold department names
$departments = [];

// Fetch subject_info and the corresponding department names
$query = "SELECT s.department_id, d.department_name 
          FROM subject_info s
          JOIN departments d ON s.department_id = d.department_id 
          GROUP BY s.department_id";

$stmt = $pdo->prepare($query);
$stmt->execute();

// Store the department names in an associative array, with department_id as the key
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[$row['department_id']] = $row['department_name'];
}

include 'includes/header.php';
?>

<div class="container bg-light py-5">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center mb-4">
                <h4><i class="fad fa-file-export"></i> Export Subject CSV File</h4>
                <p>Download subject data in CSV format quickly and easily.</p>
            </div>

            <!-- Department Selector -->
            <div class="col-md-2 col-6 mb-3">
                <label>Department:</label>
                <select id="departmentSelect" class="form-control" onchange="fetchFilters()">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department_id => $department_name): ?>
                        <option value="<?= htmlspecialchars($department_id) ?>">
                            <?= htmlspecialchars($department_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Course Selector -->
            <div class="col-md-2 col-6 mb-3">
                <label>Course:</label>
                <select id="courseSelect" class="form-control" onchange="fetchFilters()">
                    <option value="">Select Course</option>
                </select>
            </div>

            <!-- Semester Selector -->
            <div class="col-md-2 col-6 mb-3">
                <label>Semester:</label>
                <select id="semesterSelect" class="form-control">
                    <option value="">Select Semester</option>
                </select>
            </div>

            <!-- Show Button -->
            <div class="col-md-1 col-12 mb-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="fetchSubjects()">SHOW</button>
            </div>
        </div>

        <!-- Table for Subject Data -->
        <div class="row mt-4 justify-content-center">
            <div class="col-md-10">
                <div id="dataNotFound"></div>
                <div class="overflow-auto" style="max-height: 500px;">
                    <table id="studentTable" class="table table-bordered text-center">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Subject</th>
                                <th>Subject Code</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle AJAX requests -->
    <script>
    $(document).ready(function () {
        $('#departmentSelect').on('change', function () {
            fetchCourses();
            $('#semesterSelect').empty().append('<option value="">Select Semester</option>');
        });

        $('#courseSelect').on('change', function () {
            fetchSemesters();
        });
    });

    function fetchCourses() {
        var departmentId = $('#departmentSelect').val();
        if (departmentId) {
            $.ajax({
                url: 'procs/ex_get_courses.php',
                type: 'GET',
                dataType: 'json',
                data: { department_id: departmentId },
                success: function (response) {
                    var courseSelect = $('#courseSelect');
                    courseSelect.empty().append('<option value="">Select Course</option>');
                    if (response.courses && response.courses.length > 0) {
                        response.courses.forEach(function (course) {
                            courseSelect.append('<option value="' + course.course + '">' + course.course + '</option>');
                        });
                    } else {
                        courseSelect.append('<option value="">No courses available</option>');
                    }
                },
                error: function () {
                    alert('Failed to fetch courses.');
                }
            });
        } else {
            $('#courseSelect').empty().append('<option value="">Select Course</option>');
        }
    }

    function fetchSemesters() {
        var departmentId = $('#departmentSelect').val();
        var courseId = $('#courseSelect').val();
        if (departmentId && courseId) {
            $.ajax({
                url: 'procs/ex_get_semesters.php',
                type: 'GET',
                dataType: 'json',
                data: { department_id: departmentId, course_id: courseId },
                success: function (response) {
                    var semesterSelect = $('#semesterSelect');
                    semesterSelect.empty().append('<option value="">Select Semester</option>');
                    if (response.semesters && response.semesters.length > 0) {
                        response.semesters.forEach(function (sem) {
                            semesterSelect.append('<option value="' + sem.semester + '">' + sem.semester + '</option>');
                        });
                    } else {
                        semesterSelect.append('<option value="">No semesters available</option>');
                    }
                },
                error: function () {
                    alert('Failed to fetch semesters.');
                }
            });
        } else {
            $('#semesterSelect').empty().append('<option value="">Select Semester</option>');
        }
    }

    // function fetchSubjects() {
    //     var departmentId = $('#departmentSelect').val();
    //     var courseId = $('#courseSelect').val();
    //     var semesterId = $('#semesterSelect').val();

    //     if (departmentId && courseId && semesterId) {
    //         // Add your AJAX request to fetch and show subject data here
    //     } else {
    //         alert('Please select all filters.');
    //     }
    // }

function fetchSubjects() {
    var departmentId = $('#departmentSelect').val();
    var courseId = $('#courseSelect').val();
    var semesterId = $('#semesterSelect').val();

    if (departmentId && courseId && semesterId) {
        $.ajax({
            url: 'procs/ex_get_subject.php',
            type: 'GET',
            data: {
                department_id: departmentId,
                course_id: courseId,
                semester_id: semesterId
            },
            success: function(response) {
                var subjects = response.subjects;
                var tbody = $('#studentTable tbody');
                var dataNotFound = $('#dataNotFound');

                tbody.empty();
                dataNotFound.empty();

                if (subjects.length > 0) {
                    subjects.forEach(function(subject) {
                        tbody.append(
                            '<tr>' +
                                '<td>' + subject.subject + '</td>' +
                                '<td>' + subject.subject_code + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    dataNotFound.html('<div class="alert alert-warning">No subjects found for the selected filters.</div>');
                }
            },
            error: function() {
                alert('An error occurred while fetching subject data.');
            }
        });
    } else {
        alert('Please select all filters.');
    }
}



</script>

<?php include 'includes/footer.php'; ?>
