<?php include "includes/header.php"; ?>
<div class="container my-4">
    <div class="row g-0">
        <div class="col-3">
            <div class="container p-3">
                <label for="" class="">Time:</label>
                <input type="time" name="" id="" class="form-control">
            </div>
            <!-- <div class="container g-0 my-4 text-center">
                <p>Seat Plan</p>
                <button type="button" class="btn btn-primary">Submit</button>
            </div> -->
        </div>
        <div class="col-9">
            <div class="container p-3">
                <div id="dynamicFields" class="scrollable-container">
                    <div class="row fieldGroup container g-0 mb-2 p-2">
                        <div class="col-md-4">
                            <div class="container p-2 border">
                                <label for="">Department:</label>
                                <select name="department[]" class="select form-control">
                                    <option value="">default</option>
                                    <option value="Assamese">Assamese</option>
                                    <option value="English">English</option>
                                    <option value="History">History</option>
                                    <option value="Political Science">Political Science</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="container p-2 border">
                                <label for="">Course:</label>
                                <select name="course[]" class="select form-control">
                                    <option value="">default</option>
                                    <option value="UG">UG</option>
                                    <option value="PG">PG</option>
                                </select>
                            </div>                        
                        </div>
                        <div class="col-md-4">
                            <div class="container p-2 border">
                                <label for="">Semester:</label>
                                <select name="semester[]" class="select form-control">
                                    <option value="">default</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>                        
                        </div>
                    </div>
                </div>

                <div class="container my-2 d-flex justify-content-between align-items-center">
                    <p>Seat Plan <span><button type="button" class="btn btn-primary btn-sm">Generate</button></span></p>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addField()">Add</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeField()">Remove</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function addField() {
    let fieldGroup = document.querySelector('.fieldGroup'); 
    let clone = fieldGroup.cloneNode(true); 
    document.getElementById("dynamicFields").appendChild(clone); 
}

function removeField() {
    let fields = document.querySelectorAll('.fieldGroup');
    if (fields.length > 1) { 
        fields[fields.length - 1].remove(); 
    } else {
        alert("At least one set of fields must remain.");
    }
}
</script>

<?php include "includes/footer.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Table Rows with Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .scrollable-container {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
