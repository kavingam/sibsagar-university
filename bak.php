<div class="container">
    <div class="row g-0">
        <div class="col-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Exam Seat plan</h4>
        </div>
        <div class="col-3">
            <div class="time-picker-container p-3">
                <label for="startTime" class="form-label">Select Start Time</label>
                <input type="time" class="form-control custom-time" id="startTime" name="startTime">
            </div>

            <div class="container p-3">
                <label for="benchSeat">Select Bench Seat:</label>
                <select class="form-select" aria-label="Size 3 select example" id="benchSeat">
                    <option selected>Open this select menu</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="container p-3">
                <button type="button" class="btn btn-primary btn-sm" id="generate"><i class="fas fa-chevron-circle-right"></i> Generate</button>
            </div>
        </div>

        <!-- Dynamic Fields -->
        <div class="col-9">
            <div class="container p-3">
<div id="dynamicFields">
    <div class="row fieldGroup container g-0 mb-2 p-2">
        <div class="col-3">
            <?php
            include ('db/pdo_connect.php');
            $sql = 'SELECT department_id, department_name FROM departments';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="container p-2 borderx border-end-0">
                <label>Department:</label>
                <select name="department[]" class="form-control departmentSelect" onchange="fetchCoursesAndSemesters(this)">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo htmlspecialchars($department['department_id']); ?>">
                            <?php echo htmlspecialchars($department['department_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="container p-2 borderx border-start-0 border-end-0">
                <label>Course:</label>
                <select name="course[]" class="form-control courseSelect">
                    <option value="">Select Course</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="container p-2 borderx border-start-0">
                <label>Semester:</label>
                <select name="semester[]" class="form-control semesterSelect">
                    <option value="">Select Semester</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="container d-flex justify-content-center align-items-center mt-1">
                <div class="text-center">
                    <button type="button" class="btn border btn-sm w-100 mb-2" onclick="addRow()"><i class="fad fa-file-plus"></i> Add Exam</button>
                    <button type="button" class="btn border btn-sm w-100" onclick="removeRow()"><i class="fad fa-file-times"></i> Remove All</button>
                </div>
            </div>
        </div>
    </div>
</div>

            </div>

            <!-- Table -->
            <div class="container p-3">
            <div class="container p-2">
                <h6 class="text-start fs-6 text-dark text-uppercase fw-semi-bold">Examination Details</h6>
            </div>
                <div class="table-responsive p-2">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="text-center">
                            <tr>
                                <th>SNO</th>
                                <th>Department</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Total Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data added dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- code -->
    </div>
</div>