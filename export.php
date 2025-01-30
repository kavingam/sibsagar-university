<?php include 'includes/header.php'; ?>
<?php
    $sql = "SELECT COUNT(*) AS total_students FROM student";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalStudents = $row['total_students'];

    try {
        $sql = "SELECT * FROM departments";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }


?>
        
<div class="container my-4">
    <div class="row no-gutters justify-content-center d-flex">
        <div class="col-md-12 col-12 mb-3">
            <h4 class="text-center text-primary semi-bold text-uppercase">Export student csv file</h4>
        </div>
        <div class="col-md-2 col-12 mb-3">
            <label for="departmentSelect" class="form-label">Department:</label>
            <select id="departmentSelect" class="form-select">
                <option value="0" disabled selected>Select Department</option>
                <!-- <option value="1">Computer Science</option>
                <option value="2">Assamese</option>
                <option value="3">English</option>
                <option value="4">Electronics</option> -->
                <?php
                    // Check if departments were found and display them as options
                    $departments = array_reverse($departments);
                    if ($departments) {
                        foreach ($departments as $department) {
                            // Output each department as an option
                            echo "<option value='" . $department['department_id'] . "'>" . $department['department_name'] . "</option>";
                        }
                    } else {
                        echo "<option value='0'>No departments available</option>";
                    } 
                ?>                
            </select>
        </div>
        <div class="col-md-2 col-12 mb-3">
            <label for="semesterSelect" class="form-label">Semester:</label>
            <select id="semesterSelect" class="form-select">
                <option value="" disabled selected>Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
                <option value="7">Semester 7</option>
                <option value="8">Semester 8</option>
            </select>
        </div>
        <div class="col-md-2 col-12 mb-3">
            <label for="courseSelect" class="form-label">Course:</label>
            <select id="courseSelect" class="form-select">
                <option value="0" disabled selected>Select Course</option>
                <option value="1">UG</option>
                <option value="2">PG</option>
            </select>
        </div>
        <div class="col-md-1 col-12 mb-3" style="margin-top: 30px;">
            <label for="" class="form-label"></label>
            <button class="btn btn-primary" onclick="fetchStudents()">Submit</button>
        </div>
    </div>

    <div class="row mt-4 justify-content-center">
        <div class="col-md-10">
            <div id="dataNotFound"></div>
            <table id="studentTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Course</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="container my-4">
                <div class="d-flex justify-content-between">
                    <div class="p-2" id="a">
                        <h6>Total Students: <span id="totalStudents">0</span></h6>
                    </div>
                    <div class="p-2" id="b">
                        <h6>Total All Students: <span id="totalStudents"><?php echo $totalStudents; ?></span></h6>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>


<script>
/* v.0
function fetchStudents() {
    const department = document.getElementById('departmentSelect').value;
    const semester = document.getElementById('semesterSelect').value;
    const course = document.getElementById('courseSelect').value;
    if (department && semester && course) {
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
            const totalStudentsElement = document.getElementById('totalStudents');
            const dataNotFound = document.getElementById('dataNotFound');
            tableBody.innerHTML = '';
            dataNotFound.innerHTML = '';

            // If no data is returned, display "Not Found"
            if (data.length === 0) {
                dataNotFound.innerHTML = "<p class='text-center text-danger'>No students found matching your filters.</p>";
                totalStudentsElement.textContent = 0;
            } else {
                // Populate the table with student data
                data.forEach(student => {
                    let departmentName = '';
                    let courseName = '';

                    // Set department name
                    if (student.department == 1) {
                        departmentName = 'Computer Science';
                    } else if (student.department == 2) {
                        departmentName = 'Assamese';
                    } else if (student.department == 3) {
                        departmentName = 'History';
                    } else {
                        departmentName = 'Unknown';
                    }

                    // Set course name
                    if (student.course == 1) {
                        courseName = 'UG';
                    } else {
                        courseName = 'PG';
                    }

                    // Add a row for each student
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

                // Update total number of students
                totalStudentsElement.textContent = data.length;
            }
        })
        .catch(error => {
            console.error('Error fetching students:', error);
        });
    } else {
        alert('Please select all filters (Department, Semester, and Course).');
    }
}
*/
function fetchStudents() {
    const department = document.getElementById('departmentSelect').value;
    const semester = document.getElementById('semesterSelect').value;
    const course = document.getElementById('courseSelect').value;

    // Check if all filters are selected
    if (department && semester && course) {
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
            const totalStudentsElement = document.getElementById('totalStudents');
            const dataNotFound = document.getElementById('dataNotFound');

            // Clear previous results
            tableBody.innerHTML = '';
            dataNotFound.innerHTML = '';

            // If no data is returned, display "Not Found"
            if (data.length === 0) {
                dataNotFound.innerHTML = "<p class='text-center text-danger'>No students found matching your filters.</p>";
                totalStudentsElement.textContent = 0;
            } else {
                // Populate the table with student data
                data.forEach(student => {
                    // departmentName is now dynamically assigned based on the department_name returned from PHP
                    const departmentName = student.department_name || 'Unknown';  // Use department_name from the PHP response

                    // Set course name
                    let courseName = '';
                    if (student.course == 1) {
                        courseName = 'UG';
                    } else {
                        courseName = 'PG';
                    }

                    // Add a row for each student
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

                // Update total number of students
                totalStudentsElement.textContent = data.length;
            }
        })
        .catch(error => {
            console.error('Error fetching students:', error);
        });
    } else {
        alert('Please select all filters (Department, Semester, and Course).');
    }
}


</script>


<?php include 'includes/footer.php'; ?>