<?php include 'includes/header.php'; ?>
<div class="container my-4">
    <div class="row no-gutters">
        <div class="col-md-6 col-12 mb-3">
            <h5>Add Department</h5>
            <form id="departmentForm" class="w-50">
                <div class="mb-3">
                    <label for="departmentName" class="form-label">Department Name</label>
                    <input type="text" class="form-control" id="departmentName" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Department</button>
            </form>
        </div>
        <div class="col-md-6 col-12 mb-3">
            <h5>Department Table</h5>
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="departmentTableBody">
                </tbody>
            </table>
            <!-- <p>Total Departments: <span id="totalDepartments">0</span></p> -->
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm">
                    <input type="hidden" id="editDepartmentId">
                    <div class="mb-3">
                        <label for="editDepartmentName" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="editDepartmentName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
    fetchDepartments();
});

// Fetch Departments
function fetchDepartments() {
    $.get("procs/fetch_departments.php", function (response) {
        $("#departmentTableBody").html(response);

        // Attach event listeners to dynamically generated buttons
        $(".edit-btn").on("click", function () {
            let deptId = $(this).data("id");
            let deptName = $(this).data("name");

            $("#editDepartmentId").val(deptId);
            $("#editDepartmentName").val(deptName);
            $("#editDepartmentModal").modal("show");
        });

        $(".delete-btn").on("click", function () {
            let deptId = $(this).data("id");
            deleteDepartment(deptId);
        });
    });
}

// Add Department
$("#departmentForm").on("submit", function (e) {
    e.preventDefault();

    let departmentName = $("#departmentName").val().trim();
    if (departmentName === "") {
        alert("Department name cannot be empty.");
        return;
    }

    $.post("procs/add_department.php", { department_name: departmentName }, function (response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            alert("Department added successfully.");
            $("#departmentName").val("");
            fetchDepartments();
        } else {
            alert("Error: " + data.message);
        }
    });
});

// Handle Edit Form Submission
$("#editDepartmentForm").on("submit", function (e) {
    e.preventDefault();

    let departmentId = $("#editDepartmentId").val();
    let departmentName = $("#editDepartmentName").val().trim();

    if (departmentName === "") {
        alert("Department name cannot be empty.");
        return;
    }

    $.post("procs/edit_department.php", { department_id: departmentId, department_name: departmentName }, function (response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            alert("Department updated successfully.");
            $("#editDepartmentModal").modal("hide");
            fetchDepartments();
        } else {
            alert("Error: " + data.message);
        }
    });
});

// Delete Department
function deleteDepartment(deptId) {
    if (!confirm("Are you sure you want to delete this department?")) return;

    $.post("procs/delete_department.php", { department_id: deptId }, function (response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            alert("Department deleted successfully.");
            fetchDepartments();
        } else {
            alert("Error: " + data.message);
        }
    });
}

</script>

<script>
    
</script>
<?php include 'includes/footer.php'; ?>