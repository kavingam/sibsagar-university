<?php 
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
?>

<?php include 'includes/header.php'; 
include 'xyz/bashmodel.php';
$stdObj = new Student();
?>

<div class="container" style="margin-top:100px">
<h5>
    Student Management Panel | Currently Enrolled Students: 
    <span class="text-success fw-bold"><?php echo count($stdObj->getAllStudents()); ?></span>
</h5>

    <table id="student-table" class="table table-bordered text-center">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Department</th>
                <th>Course</th>
                <th>Semester</th>
                <th>Total Students</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="student-table-body">
            <!-- JS will populate this -->
        </tbody>
    </table>
</div>

<!-- Full Screen Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollablex modal-fullscreen">
        <div class="modal-content">
            <!-- <div class="modal-header">
                 <h5 class="modal-title" id="studentModalLabel">Student Manage</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button type="button" class="btn btn-red me-auto" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div> -->


            <div class="modal-header justify-content-between">
                <h5 class="modal-title mb-0" id="studentModalLabel">Student Details</h5>
                <div>
                    <button type="button" class="btn btn-red" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div class="container">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <h5 class="mb-0">Department: <span id="modalDepartmentName"></span></h5>
                        <h5 class="mb-0">Course: <span id="modalCourseName"></span></h5>
                        <h5 class="mb-0">Semester: <span id="modalSemesterName"></span></h5>
                    </div>

                    <table class="table table-bordered" id="studentListTable">
                        <thead class="text-center">
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Roll No</th>
                                <th>Department</th>
                                <th>Course</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let departmentList = {};
let courseList = {
    1: 'UG',
    2: 'PG',
    3: 'TDC',
    4: 'FYUG'
};

// Load department names from API
function loadDepartments() {
    fetch('xyz/api/get_departments_api.php')
        .then(res => res.json())
        .then(data => {
            data.forEach(d => {
                departmentList[parseInt(d.department_id)] = d.department_name;
            });
            loadStudents();
        })
        .catch(err => {
            console.error('Error loading departments:', err);
            alert('Failed to load departments.');
        });
}

function loadStudents() {
    fetch('xyz/api/students_api.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('student-table-body');
            tbody.innerHTML = '';
            const groupMap = {};

            data.forEach(student => {
                const dept = parseInt(student.department);
                const courseId = parseInt(student.course);
                const sem = student.semester;
                const key = `${dept}-${courseId}-${sem}`;

                if (!groupMap[key]) {
                    groupMap[key] = {
                        department: dept,
                        course: courseId,
                        semester: sem,
                        count: 0
                    };
                }
                groupMap[key].count++;
            });

            // Object.values(groupMap).forEach(group => {
            //     const row = document.createElement('tr');
            //     row.innerHTML = `
            //         <td></td>
            //         <td class="text-start">${departmentList[group.department] || 'Unknown Department'}</td>
            //         <td>${courseList[group.course] || 'Unknown Course'}</td>
            //         <td>${group.semester}</td>
            //         <td>${group.count}</td>

            //         <td class="text-center">
            //             <div class="btn-groupx">
            //                 <a href="#" class="btn btn-lightweight btn-primary-light btn-sm " onclick="viewGroup(${group.department}, ${group.course}, ${group.semester})">
            //                     <i class="fas fa-eye"></i> View
            //                 </a>
            //                 <a href="#" class="btn btn-lightweight btn-success-light btn-sm" onclick="editGroup(${group.department}, ${group.course}, ${group.semester})">
            //                     <i class="fas fa-edit"></i> Edit
            //                 </a>
            //                 <a href="#" class="btn btn-lightweight btn-danger-light btn-sm" onclick="deleteGroup(${group.department}, ${group.course}, ${group.semester})">
            //                     <i class="fas fa-trash-alt"></i> Delete All
            //                 </a>
            //             </div>
            //         </td>

            //     `;
            //     tbody.appendChild(row);
            // });
            Object.values(groupMap).forEach((group, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td class="text-start">${departmentList[group.department] || 'Unknown Department'}</td>
                    <td>${courseList[group.course] || 'Unknown Course'}</td>
                    <td>${group.semester}</td>
                    <td>${group.count}</td>

                    <td class="text-center">
                        <div class="btn-groupx">
                            <a href="#" class="btn btn-lightweight btn-primary-light btn-sm" onclick="viewGroup(${group.department}, ${group.course}, ${group.semester})">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="#" class="btn btn-lightweight btn-success-light btn-sm" onclick="editGroup(${group.department}, ${group.course}, ${group.semester})">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" class="btn btn-lightweight btn-danger-light btn-sm" onclick="deleteGroup(${group.department}, ${group.course}, ${group.semester})">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });


        })
        .catch(err => {
            console.error('Error loading student data:', err);
            alert('Failed to load student groups.');
        });
}

function viewGroup(department, course, semester) {
    console.log("viewGroup called with:", department, course, semester);

    const formData = new URLSearchParams();
    formData.append('department', department);
    formData.append('course', course);
    formData.append('semester', semester);

    fetch('xyz/api/get_students_by_group.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString(),
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modalDepartmentName').textContent = departmentList[department] || 'Unknown Department';
        document.getElementById('modalCourseName').textContent = courseList[course] || 'Unknown Course';
        document.getElementById('modalSemesterName').textContent = semester;

        const tbody = document.getElementById('studentTableBody');
        tbody.innerHTML = '';
        data.forEach((student, i) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${i + 1}</td>
                <td>${student.name}</td>
                <td>${student.roll_no}</td>
                <td class="text-start">${departmentList[department] || 'Unknown Department'}</td>
                <td>${courseList[parseInt(student.course)] || 'Unknown Course'}</td>
            `;
            tbody.appendChild(row);
        });

        const modal = new bootstrap.Modal(document.getElementById('studentModal'));
        modal.show();
    })
    .catch(err => {
        console.error('Error loading student list:', err);
        alert('Failed to load student list.');
    });
}

function editGroup(department, course, semester) {
    alert(`Edit group: Dept ${department}, Course ${course}, Semester ${semester}`);
}

function deleteGroup(department, course, semester) {
    if (!confirm(`Delete ALL students in Dept ${department}, Course ${course}, Semester ${semester}?`)) return;

    const formData = new URLSearchParams();
    formData.append('department', department);
    formData.append('course', course);
    formData.append('semester', semester);

    fetch('xyz/api/delete_student.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.success) loadStudents();
    })
    .catch(err => {
        console.error('Error deleting group:', err);
        alert('Delete failed.');
    });
}

// Load everything on page ready
document.addEventListener('DOMContentLoaded', loadDepartments);
</script>

<?php include 'includes/footer.php'; ?>
