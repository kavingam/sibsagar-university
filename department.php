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
                <button type="submit" class="btn btn-primary"><i class="fal fa-plus text-white fw-bold"></i> Add Department</button>
            </form>
        </div>
        <div class="col-md-6 col-12 mb-3">
            <h5>Department Details</h5>
            <div class="overflow-auto table-responsive" style="max-height: 720px;">
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="text-center bg-dark text-white">
                    <tr>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="departmentTableBody">
                </tbody>
            </table>
            </div>
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
                    <button type="submit" class="btn btn-primary"><i class="far fa-pen-square"></i> Update Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>