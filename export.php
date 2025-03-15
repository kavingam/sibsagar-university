<?php include 'includes/header.php'; ?>
<?php include ('db/pdo_connect.php'); ?>

<?php
// Fetch total students count
$sql = 'SELECT COUNT(*) AS total_students FROM student';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalStudents = $row['total_students'];

// Fetch all departments
try {
    $sql = 'SELECT department_id, department_name FROM departments';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<div class="container my-4">
    <div class="row no-gutters fieldGroup justify-content-center d-flex">
        <div class="col-md-12 col-12 mb-3">
            <h4 class="text-center text-primary semi-bold text-uppercase">Export Student CSV File</h4>
        </div>
        
        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label>Department:</label>
                <select id="departmentSelect" name="department" class="form-control departmentSelect" onchange="fetchCoursesAndSemesters(this)">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department['department_id']); ?>">
                            <?= htmlspecialchars($department['department_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-2 col-12 mb-3">
           <div class="container p-2">
                <label>Course:</label>
                <select id="courseSelect" name="course" class="form-control courseSelect">
                    <option value="">Select Course</option>
                </select>
            </div>
        </div>

        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label>Semester:</label>
                <select id="semesterSelect" name="semester" class="form-control semesterSelect">
                    <option value="">Select Semester</option>
                </select>
            </div>
        </div>

        <div class="col-md-1 col-12 mb-3" style="margin-top: 30px;">
            <button class="btn btn-primary" onclick="fetchStudents()">SHOW</button>
        </div>
    </div>

    <div class="row mt-4 justify-content-center">
        <div class="col-md-10">
            <div id="dataNotFound"></div>
            <table id="studentTable" class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Course</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="container my-4">
                <div class="d-flex justify-content-between">
                    <div class="p-2">
                        <h6>Total Students (Filtered): <span id="totalStudentsFiltered">0</span></h6>
                    </div>
                    <div class="p-2">
                        <h6>Total All Students: <span id="totalStudents"><?= $totalStudents; ?></span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fetchStudents() {
    const department = document.getElementById('departmentSelect').value;
    const semester = document.getElementById('semesterSelect').value;
    const course = document.getElementById('courseSelect').value;

    if (!department || !semester || !course) {
        alert('Please select all filters (Department, Semester, and Course).');
        return;
    }

    const formData = new FormData();
    formData.append('department', department);
    formData.append('semester', semester);
    formData.append('course', course);

    fetch('procs/export_std.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.querySelector('#studentTable tbody');
        const totalStudentsElement = document.getElementById('totalStudentsFiltered');
        const dataNotFound = document.getElementById('dataNotFound');

        tableBody.innerHTML = '';
        dataNotFound.innerHTML = '';

        if (data.length === 0) {
            dataNotFound.innerHTML = "<p class='text-center text-danger'>No students found matching your filters.</p>";
            totalStudentsElement.textContent = 0;
        } else {
            const courseMap = {
                1: "UG",
                2: "PG",
                3: "TDC",
                4: "FYUG"
            };

            data.forEach(student => {
                const departmentName = student.department_name || 'Unknown';
                const courseName = courseMap[student.course] || 'Unknown';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.roll_no}</td>
                    <td>${student.name}</td>
                    <td>${departmentName}</td>
                    <td>${student.semester}</td>
                    <td>${courseName}</td>
                `;
                tableBody.appendChild(row);
            });

            totalStudentsElement.textContent = data.length;
        }
    })
    .catch(error => {
        console.error('Error fetching students:', error);
    });
}

/*function fetchStudents() {
    const department = document.getElementById('departmentSelect').value;
    const semester = document.getElementById('semesterSelect').value;
    const course = document.getElementById('courseSelect').value;

    if (!department || !semester || !course) {
        alert('Please select all filters (Department, Semester, and Course).');
        return;
    }

    const formData = new FormData();
    formData.append('department', department);
    formData.append('semester', semester);
    formData.append('course', course);

    fetch('procs/export_std.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.querySelector('#studentTable tbody');
        const totalStudentsElement = document.getElementById('totalStudentsFiltered');
        const dataNotFound = document.getElementById('dataNotFound');

        tableBody.innerHTML = '';
        dataNotFound.innerHTML = '';

        if (data.length === 0) {
            dataNotFound.innerHTML = "<p class='text-center text-danger'>No students found matching your filters.</p>";
            totalStudentsElement.textContent = 0;
        } else {
            data.forEach(student => {
                const departmentName = student.department_name || 'Unknown';
                const courseName = student.course == 1 ? 'UG' : 'PG';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.roll_no}</td>
                    <td>${student.name}</td>
                    <td>${departmentName}</td>
                    <td>${student.semester}</td>
                    <td>${courseName}</td>
                `;
                tableBody.appendChild(row);
            });

            totalStudentsElement.textContent = data.length;
        }
    })
    .catch(error => {
        console.error('Error fetching students:', error);
    });
}
*/
function fetchCoursesAndSemesters(selectElement) {
    var departmentId = selectElement.value;
    var row = selectElement.closest('.fieldGroup');

    if (departmentId) {
        fetch(`procs/fetch_courses_semesters.php?department_id=${departmentId}`)
        .then(response => response.json())
        .then(data => {
            var courseDropdown = row.querySelector('.courseSelect');
            var semesterDropdown = row.querySelector('.semesterSelect');

            courseDropdown.innerHTML = '<option value="">Select Course</option>';
            semesterDropdown.innerHTML = '<option value="">Select Semester</option>';

            data.courses.forEach(course => {
                courseDropdown.innerHTML += `<option value="${course.id}">${course.name}</option>`;
            });

            data.semesters.forEach(semester => {
                semesterDropdown.innerHTML += `<option value="${semester}">Semester ${semester}</option>`;
            });
        })
        .catch(error => console.error('Error fetching data:', error));
    }
}
</script>

<?php include 'includes/footer.php'; ?>
