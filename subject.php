<?php 
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container" style="margin-top:100px">
<h4>Subject Titles & Codes</h4>


<table class="table table-bordered table-striped text-center" id="subjectTable">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Department Name</th>
      <th>Semester</th>
      <th>Course</th>
      <th>Total Subjects</th>
      <!-- <th>Subject </th> -->
      <th>Actions</th>
    </tr>
  </thead>
  <tbody id="subjectTableBody">
    <!-- rows loaded dynamically -->
  </tbody>
</table>


</div>
<!-- Full screen modal view subject and code list -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true" aria-labelledby="subjectModalLabel">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      
      <!-- <div class="modal-header">
        <h5 class="modal-title" id="subjectModalLabel">Subjects in Group</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->
      <div class="modal-header justify-content-between">
          <h5 class="modal-title mb-0" id="studentModalLabel">Subject And Paper Code Details</h5>
          <div>
              <button type="button" class="btn btn-red" data-bs-dismiss="modal" aria-label="Close">
              <i class="fas fa-arrow-left"></i> Back
              </button>
          </div>
      </div>

      <div class="modal-body">
      <div class="container">
          <!-- Info row -->
          <div class="mb-3">
            <strong>Department:</strong> <span id="modalDepartmentName"></span> |
            <strong>Course:</strong> <span id="modalCourseName"></span> |
            <strong>Semester:</strong> <span id="modalSemester"></span> |
            <strong>Total Subjects:</strong> <span id="modalTotalSubjects"></span>
          </div>

          <!-- Subjects Table -->
          <!-- <table class="table table-borderedx table-stripedx table-hover">
            <thead class="table-darkx">
              <tr class="text-center">
                <th>S.No</th>
                <th>Subject Title</th>
                <th>Subject Code</th>
              </tr>
            </thead>
            <tbody id="subjectTableBodyx">
            </tbody>
          </table> -->
          <table class="table table-bordered table-striped table-hover">
          <thead>
  <tr class="text-center">
    <th>S.No</th>
    <th>Subject Title</th>
    <th>Subject Code</th>
  </tr>
</thead>

  <tbody id="subjectTableBodyx">
    <!-- Filled dynamically -->
  </tbody>
</table>


        </div>
      </div>
    </div>
  </div>
</div>


<script>
const courseMap = {
  1: 'UG',
  2: 'PG',
  3: 'TDC'
};

function loadGroupedSubjects() {
  fetch('xyz/api/get_grouped_subjects.php')
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('subjectTableBody');
      tbody.innerHTML = '';

      data.forEach((group, index) => {
        // Subject codes concatenated, max 5 codes shown, rest as "..."
        let codes = group.subject_codes;
        if (codes.length > 5) {
          codes = codes.slice(0, 5).join(', ') + ', ...';
        } else {
          codes = codes.join(', ');
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td class="text-start">${group.department_name}</td>
          <td>${group.semester}</td>
          <td>${courseMap[group.course] || 'Unknown'}</td>
          <td>${group.total_subjects}</td>


          

            <td class="text-center">
              <div class="btn-groupx d-flex gap-2" role="group" aria-label="Actions" style="gap: 2px;">
                <button class="btn btn-lightweight btn-info-light btn-sm" onclick="viewGroup(${group.department_id}, ${group.course}, ${group.semester})">
                  <i class="fal fa-eye"></i> View
                </button>
                <button class="btn btn-lightweight btn-success-light btn-sm" onclick="editGroup(${group.department_id}, ${group.course}, ${group.semester})">
                  <i class="fal fa-edit"></i> Edit
                </button>
                <button class="btn btn-lightweight btn-danger-light btn-sm" onclick="deleteGroup(${group.department_id}, ${group.course}, ${group.semester})">
                  <i class="fal fa-trash-alt"></i> Delete
                </button>
              </div>
            </td>



        `;
        tbody.appendChild(tr);
      });
    })
    .catch(err => {
      console.error('Error loading grouped subjects:', err);
      alert('Failed to load grouped subject data.');
    });
}

// <td>
//   <div class="btn-group" role="group" aria-label="Actions">
//     <button class="btn btn-info btn-sm" onclick="viewGroup(${group.department_id}, ${group.course}, ${group.semester})">View</button>
//     <button class="btn btn-warning btn-sm" onclick="editGroup(${group.department_id}, ${group.course}, ${group.semester})">Edit</button>
//     <button class="btn btn-danger btn-sm" onclick="deleteGroup(${group.department_id}, ${group.course}, ${group.semester})">Delete</button>
//   </div>
// </td>

let departmentList = {};

function loadDepartments() {
  fetch('xyz/api/get_departments_api.php')
    .then(res => res.json())
    .then(data => {
      data.forEach(d => {
        departmentList[parseInt(d.department_id)] = d.department_name;
      });
    })
    .catch(err => {
      console.error('Failed to load departments', err);
    });
}

// Call this on page load before you use viewGroup
loadDepartments();

function viewGroup(department, course, semester) {
  // Show loading or reset modal content
  document.getElementById('modalDepartmentName').textContent = '';
  document.getElementById('modalCourseName').textContent = '';
  document.getElementById('modalSemester').textContent = '';
  document.getElementById('modalTotalSubjects').textContent = '';
  const tbody = document.getElementById('subjectTableBodyx');
  tbody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

  const courseList = {
    1: "UG",
    2: "PG",
    3: "TDC",
    4: "FYUG"
  };

  // Fill header info now
  document.getElementById('modalDepartmentName').textContent = departmentList[department] || "Unknown Department";
  document.getElementById('modalCourseName').textContent = courseList[course] || "Unknown Course";
  document.getElementById('modalSemester').textContent = semester;

  // Fetch subject data for this group
  const formData = new URLSearchParams();
  formData.append('department', department);
  formData.append('course', course);
  formData.append('semester', semester);

  fetch('xyz/api/get_subjects_by_groupx.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString(),
  })
  .then(res => res.json())
  .then(data => {
    if (data.error) {
      tbody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">${data.error}</td></tr>`;
      document.getElementById('modalTotalSubjects').textContent = '0';
      return;
    }
    if (data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="3" class="text-center">No subjects found for this group.</td></tr>`;
      document.getElementById('modalTotalSubjects').textContent = '0';
      return;
    }

    document.getElementById('modalTotalSubjects').textContent = data.length;

    tbody.innerHTML = ''; // Clear loading text
    data.forEach((subject, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${subject.subject}</td>
        <td>${subject.subject_code}</td>
      `;
      tbody.appendChild(row);
    });

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('subjectModal'));
    modal.show();
  })
  .catch(err => {
    tbody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">Failed to load subjects.</td></tr>`;
    document.getElementById('modalTotalSubjects').textContent = '0';
    console.error('Error fetching subjects:', err);
  });
}

function editGroup(department, course, semester) {
  alert(`Edit group: Dept ${department}, Course ${course}, Semester ${semester}`);
  // Implement your edit logic here
}

// function deleteGroup(department, course, semester) {
//   if (!confirm(`Delete ALL subjects in Dept ${department}, Course ${course}, Semester ${semester}?`)) return;
//   alert(`Deleting group: Dept ${department}, Course ${course}, Semester ${semester}`);
//   // Implement your delete logic here
// }

function deleteGroup(department, course, semester) {
  if (!confirm(`Delete ALL subjects in Dept ${department}, Course ${course}, Semester ${semester}?`)) return;

  fetch('xyz/api/delete_group_subjects.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ department_id: department, course, semester })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      alert('Subjects deleted successfully.');
      loadGroupedSubjects(); // Refresh the table
    } else {
      alert('Delete failed: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => {
    console.error('Delete error:', err);
    alert('Error deleting subjects.');
  });
}


document.addEventListener('DOMContentLoaded', loadGroupedSubjects);
</script>

<?php include 'includes/footer.php'; ?>