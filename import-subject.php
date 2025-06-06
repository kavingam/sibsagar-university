<?php 
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}
include 'includes/header.php';

try {
    $sql = 'SELECT * FROM departments';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<div class="container bg-light vh-100">
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12 col-12 import-section my-5">
                <h2 class="section-title"><i class="fad fa-file-import"></i> Import Subject & Paper Code CSV File</h2>
                <p class="section-subtext">Easily upload and manage subject data in CSV format</p>
            </div>

            <div class="col-md-2 col-12 mb-3">
                <label for="departmentSelect" class="form-label">Department:</label>
                <select id="departmentSelect" name="department_id" class="form-select" required>
                    <option value="" disabled selected>Select Department</option>
                    <?php
                    if ($departments) {
                        foreach ($departments as $department) {
                            echo "<option value='" . $department['department_id'] . "'>" . $department['department_name'] . '</option>';
                        }
                    } else {
                        echo "<option value=''>No departments available</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2 col-12 mb-3">
                <label for="semesterSelect" class="form-label">Semester:</label>
                <select id="semesterSelect" name="semester" class="form-select" required>
                    <option value="" disabled selected>Select Semester</option>
                    <?php for ($i = 1; $i <= 8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
                </select>
            </div>

            <div class="col-md-2 col-12 mb-3">
                <label for="courseSelect" class="form-label">Course:</label>
                <select id="courseSelect" name="course" class="form-select" required>
                    <option value="" disabled selected>Select Course</option>
                    <?php
                    $courses = [1 => 'UG', 2 => 'PG', 3 => 'TDC', 4 => 'FYUG'];
                    foreach ($courses as $value => $name) {
                        echo "<option value='$value'>$name</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 col-12 mb-3">
                <label for="csvFile" class="form-label">Import CSV File:</label>
                <input type="file" id="csvFile" name="csvFile" class="form-control" accept=".csv" required>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3 d-flex justify-content-center align-items-end">
                <button type="submit" id="submitButton" class="btn btn-green w-100">
                    <i class="bi bi-database-add"></i> IMPORT
                </button>
            </div>

            <div id="loadingSpinner" class="text-center mt-3" style="display:none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div class="col-12 mt-3" id="responseMessage"></div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                e.stopPropagation();
                return;
            }
            $('#submitButton').prop('disabled', true);
            $('#loadingSpinner').show();
            var formData = new FormData(this);
            $.ajax({
                url: 'procs/import_sub.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === "success") {
                        $('#responseMessage').html('<div class="alert alert-success">' +
                            response.message + '</div>');
                        /*
                        if (response.duplicates && response.duplicates.length > 0) {
                            var duplicateList = response.duplicates.join(", ");
                            $('#duplicateModalBody').html("Duplicate roll numbers: " + duplicateList);
                            $('#duplicateModal').modal('show');
                        }
                        */
                    } 
                    else {
                        $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = "Duplicate Entry Detected... please try again";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('#responseMessage').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                },
                complete: function() {
                    $('#submitButton').prop('disabled', false);
                    $('#loadingSpinner').hide();
                }
            });
        });
    });
</script>


<?php include 'includes/footer.php'; ?>
