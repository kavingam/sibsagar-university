<?php include 'includes/header.php'; ?>
<div class="container my-4">
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="row no-gutters">
            <div class="col-md-12 col-12 mb-3">
                <h4 class="text-center text-primary semi-bold text-uppercase">Import Student CSV File</h4>
            </div>
            <div class="col-md-2 col-12 mb-3">
                <label for="departmentSelect" class="form-label">Department:</label>
                <select id="departmentSelect" name="department" class="form-select" required>
                    <option value="0" disabled selected>Select Department</option>
                    <option value="1">Computer Science</option>
                    <option value="2">Assamese</option>
                    <option value="3">English</option>
                    <option value="4">Electronics</option>
                    
                </select>
            </div>
            <div class="col-md-2 col-12 mb-3">
                <label for="semesterSelect" class="form-label">Semester:</label>
                <select id="semesterSelect" name="semester" class="form-select" required>
                    <option value="0" disabled selected>Select Semester</option>
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
                <select id="courseSelect" name="course" class="form-select" required>
                    <option value="0" disabled selected>Select Course</option>
                    <option value="1">UG</option>
                    <option value="2">PG</option>
                </select>
            </div>
            <div class="col-md-3 col-12 mb-3">
                <label for="csvFile" class="form-label">Import CSV File:</label>
                <input type="file" id="csvFile" name="csvFile" class="form-control" accept=".csv" required>
            </div>
            <div class="col-md-1 col-12 mb-3" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <div id="responseMessage"></div>
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
                url: 'procs/import_std.php',
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