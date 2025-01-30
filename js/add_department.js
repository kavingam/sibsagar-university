$(document).ready(function() {
  fetchDepartments();
});

$("#departmentForm").on("submit", function(e) {
  e.preventDefault();

  let departmentId = $("#departmentId").val();
  let departmentName = $("#departmentName").val();

  $.ajax({
    url: "procs/add_department.php",
    type: "POST",
    data: {
      department_id: departmentId,
      department_name: departmentName
    },
    success: function(response) {
      let data = JSON.parse(response);
      $("#departmentId").val("");
      $("#departmentName").val("");
      
      if (data.status === 'exists_name') {
        alert("Error: " + data.message); // Department name already exists
      } else if (data.status === 'exists') {
        alert("Department ID already exists. Updated department name.");
      } else if (data.status === 'success') {
        alert("Department added successfully.");
      } else {
        alert("Error: " + data.message);
      }
      fetchDepartments();
    },
    error: function(xhr, status, error) {
      alert("There was an error with the request: " + error);
    }
  });
});

function fetchDepartments() {
  $.ajax({
    url: "procs/fetch_departments.php",
    type: "GET",
    success: function(response) {
      $("#departmentTableBody").html(response);
    }
  });
}
