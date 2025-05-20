<?php 
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
?>
<?php 

include 'includes/header.php'; 

?>
<div class="container bg-light vh-100">
    <div class="row g-0">
        <div class="col-md-12 col-12 import-section my-5">
            <h2 class="section-title"><i class="fas fa-th-large"></i> Advanced Exam Seat Plan</h2>
            <p class="section-subtext">Generate and manage detailed seating arrangements for examinations</p>
        </div>

        <div class="col-3 mt-4">
            <div class="container">
                <div class="mb-3" id="examSelectContainer">
                    <label for="examSelect" class="form-label">Select Exam</label>
                    <select class="form-select" id="examSelect" onchange="selectExam()">
                        <option value="" disabled selected>Select an exam</option>
                        <option value="Class Test">Class Test</option>
                        <option value="Sessional">Sessional</option>
                        <option value="Midterm Exam">Midterm Exam</option>
                        <option value="Final Examination">Final Examination</option>
                    </select>
                </div>

                <div class="mb-3" id="examInputContainer" style="display: none;">
                    <label for="examInput" class="form-label">Enter Exam Name</label>
                    <input type="text" class="form-control" id="examInput" name="examInput" placeholder="Enter exam name"
                        oninput="selectInput()" disabled>
                </div>

                <div class="mb-3">
                    <button class="btn btn-primary btn-sm" id="toggleButton" onclick="toggleSelection()">Switch to Exam Name</button>
                </div>

                <div class="time-picker-container">
                    <label for="startDate" class="form-label">Select Date</label>
                    <input type="date" class="form-control custom-date" id="startDate" name="startDate">
                </div>

                <div class="time-picker-container p-3">
                    <label for="startTime" class="form-label">Select Start Time</label>
                    <input type="time" class="form-control custom-time" id="startTime" name="startTime">
                </div>

                <div class="container p-3">
                    <label for="benchSeat">Select Bench Seat:</label>
                    <select class="form-select" aria-label="Size 3 select example" id="benchSeat">
                        <option selected disabled>Choose Number of Seats Per Bench</option>
                        <option value="1">Bench with 1 Seat</option>
                        <option value="2">Bench with 2 Seats</option>
                    </select>
                </div>

                <div class="container p-3">
                    <button type="button" class="btn btn-primary btn-sm" id="generate"><i class="fas fa-chevron-circle-right"></i> Generate</button>
                </div>
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
                            <div class="container p-2 borderx border-start-0">
                                <label>Subject:</label>
                                <select name="subject[]" class="form-control subjectSelect">
                                    <option value="">Select Subject</option>
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
                    <!-- <table class="table table-bordered" id="dataTable">
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
                        </tbody>
                    </table> -->


                    <table class="table table-bordered" id="tableBody">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Department</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Subject</th>
                                <th>Total Students</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Rows will be added here -->
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full-Screen Modal -->
<div class="modal fade" id="generatedDataModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-center">Generate Seat Plan / Download Report </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalBodyContent" class="">
                    <!-- PHP returned content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="downloadPDF()"><i class="fas fa-file-pdf"></i> Download PDF</button>
                <button class="btn btn-secondary" onclick="printContent()"><i class="fad fa-print"></i> Print</button>
                <button class="btn btn-danger" data-bs-dismiss="modal"><i class="fad fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// function downloadPDF() {
//     const element = document.getElementById('printSection');
//     const opt = {
//         margin:       0.5,
//         filename:     'Seat-Allotment-Report.pdf',
//         image:        { type: 'jpeg', quality: 0.98 },
//         html2canvas:  { scale: 2 },
//         jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
//     };
//     html2pdf().set(opt).from(element).save();
// }

function downloadPDF() {
    const element = document.getElementById("modalBodyContent");

    if (!element) {
        console.error("Element with ID 'modalBodyContent' not found.");
        return;
    }

    const options = {
        margin: 0, 
        filename: 'seat_plan.pdf',
        image: { type: 'jpeg', quality: 1 }, 
        html2canvas: {
            scale: 3,
            useCORS: true,
            logging: false,
        },
        jsPDF: {
            unit: 'mm',
            format: 'letter',
            orientation: 'portrait'
        }
    };

    element.style.backgroundColor = "#ffffff"; // Ensure the background is white

    html2pdf()
        .set(options)
        .from(element)
        .toPdf()
        .get('pdf')
        .then(function (pdf) {
            let totalPages = pdf.internal.getNumberOfPages();
            pdf.setFontSize(10);
            for (let i = 1; i <= totalPages; i++) {
                pdf.text('Page ' + i + ' of ' + totalPages, 200, 290); // Add page numbers
            }
        })
        .save();
}

</script>

<script>

// Function to print the seat plan content
function printContent() {
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(document.getElementById('modalBodyContent').innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Function to print the seat plan content
// function printContent() {
//     // Open a new window for printing
//     var printWindow = window.open('', '_blank');
    
//     // Add HTML structure to the new print window
//     printWindow.document.write('<html><head><title>Print Seat Plan</title>');
//     printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">');
//     // Add necessary CSS styles for printing
//     printWindow.document.write('<style>');
//     printWindow.document.write(`
//         @page { 
//             size: A4 portrait; 
//             margin: 1cm; 
//         }
//     `);
//     printWindow.document.write('</style>');
    
//     // Add content from the modal to the print window
//     printWindow.document.write('</head><body>');
//     printWindow.document.write('<h2>Seat Plan</h2>');  // Title for the printout
//     printWindow.document.write(document.getElementById('modalBodyContent').innerHTML);  // Seat plan content from modal
//     printWindow.document.write('</body></html>');
    
//     // Close the document to ensure everything is rendered properly
//     printWindow.document.close();
    
//     // Wait for content to load and then trigger the print dialog
//     setTimeout(function () {
//         printWindow.print();  // Trigger the print dialog
//         printWindow.close();  // Close the print window after printing
//     }, 1000);  // Delay to ensure content has loaded before printing
// }



// function printContent() {
//     const content = document.getElementById("printSection").innerHTML;

//     // Open a new window for printing
//     const win = window.open('', '', 'height=900,width=800');
    
//     // Write the HTML and CSS to the new window
//     win.document.write('<html><head><title>Print</title>');
//     win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
//     win.document.write('<style>');
//     win.document.write(`
//         @page { size: A4 portrait; margin: 1cm; }
//         body { font-size: 11pt; }
//         .student-cardx { border: 1px solid #000; padding: 5px; margin-bottom: 5px; border-radius: 4px; page-break-inside: avoid; }
//         .container.mb-5 { page-break-inside: avoid; margin-bottom: 10px; break-after: page; }
//         .row { page-break-inside: avoid; }
//     `);
//     win.document.write('</style></head><body>');
//     win.document.write(content);
//     win.document.write('</body></html>');
//     win.document.close();  // Important: closes the document to allow rendering
    
//     // Give the new window a moment to fully load before printing
//     setTimeout(function () {
//         win.focus();  // Focus on the new window
//         win.print();  // Trigger the print dialog
//         win.close();  // Close the print window
//     }, 1000);  // 1-second delay to ensure everything is rendered
// }

// Function to switch between "Exam Type" and "Exam Name"
function toggleSelection() {
    var examSelectContainer = document.getElementById('examSelectContainer');
    var examInputContainer = document.getElementById('examInputContainer');
    var toggleButton = document.getElementById('toggleButton');
    
    if (examSelectContainer.style.display === 'none') {
        examSelectContainer.style.display = 'block';
        examInputContainer.style.display = 'none';
        toggleButton.innerHTML = 'Switch to Exam Name';
    } else {
        examSelectContainer.style.display = 'none';
        examInputContainer.style.display = 'block';
        toggleButton.innerHTML = 'Switch to Exam Type';
    }
}

// Function to enable input for "Exam Name" based on selection
function selectInput() {
    var examInput = document.getElementById('examInput');
    examInput.disabled = !examInput.value;
}

// Function to handle the department selection and fetch associated courses/semesters
function fetchCoursesAndSemesters(selectElement) {
    var departmentId = selectElement.value;
    var courseSelect = selectElement.closest('.fieldGroup').querySelector('.courseSelect');
    var semesterSelect = selectElement.closest('.fieldGroup').querySelector('.semesterSelect');
    
    if (departmentId) {
        // Fetch courses based on department selection
        fetch('get_courses.php?department_id=' + departmentId)
            .then(response => response.json())
            .then(data => {
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                data.courses.forEach(course => {
                    var option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.name;
                    courseSelect.appendChild(option);
                });
            });
        
        // Fetch semesters based on department selection
        fetch('get_semesters.php?department_id=' + departmentId)
            .then(response => response.json())
            .then(data => {
                semesterSelect.innerHTML = '<option value="">Select Semester</option>';
                data.semesters.forEach(semester => {
                    var option = document.createElement('option');
                    option.value = semester.id;
                    option.textContent = semester.name;
                    semesterSelect.appendChild(option);
                });
            });
    }
}

// Function to add a new row to the dynamic form
// function addRow() {
//     var rowContainer = document.getElementById('dynamicFields');
//     var newRow = rowContainer.querySelector('.fieldGroup').cloneNode(true);
    
//     // Reset the select elements for the new row
//     var selects = newRow.querySelectorAll('select');
//     selects.forEach(select => select.selectedIndex = 0);
    
//     rowContainer.appendChild(newRow);
// }

function removeRow() {
    var rowContainer = document.getElementById('dynamicFields');
    var rows = rowContainer.querySelectorAll('.fieldGroup');
    
    // Loop through all rows, and remove them except for the first one
    rows.forEach((row, index) => {
        if (index !== 0) {
            rowContainer.removeChild(row);
        }
    });
}
// Function to delete row
function deleteRow(button) {
    button.closest('tr').remove();
}


function removeRow() {
    const tableBody = document.getElementById('tableBody');
    if (tableBody.children.length > 0) {
        tableBody.removeChild(tableBody.lastChild);
        rowCount--;
    } else {
        alert('No rows to remove');
    }
}

function deleteRow(button) {
    button.closest('tr').remove();
    rowCount--;
}




// Function to download the seat plan as a PDF

// document.getElementById('generate').addEventListener('click', function () {
//     const startTime = document.getElementById('startTime').value.trim();
//     const benchSeat = document.getElementById('benchSeat').value.trim();
//     const selectedExam = document.getElementById('examSelect').value.trim();
//     const enteredExamName = document.getElementById('examInput').value.trim();
//     const startDate = document.getElementById('startDate').value.trim();
//     const rows = document.querySelectorAll("#tableBody tr");

//     if (!startDate || (!selectedExam && !enteredExamName) || rows.length === 0 || !startTime || isNaN(benchSeat)) {
//         alert("Please fill all required fields.");
//         return;
//     }

//     // let tableData = [];
//     // rows.forEach(row => {
//     //     let department = row.cells[1]?.textContent.trim();
//     //     let course = row.cells[2]?.textContent.trim();
//     //     let semester = row.cells[3]?.textContent.trim();
//     //     let subject = row.cells[4]?.textContent.trim();
//     //     let totalStudent = row.cells[5]?.textContent.trim();

//     //     if (department && course && semester && subject && totalStudent) {
//     //         tableData.push({ department, course, semester, subject, totalStudent });
//     //     }
//     // });

//     let tableData = [];
//     rows.forEach((row, index) => {
//         if (index === 0) return; // Skip header row

//         let department = row.cells[1]?.textContent.trim();
//         let course = row.cells[2]?.textContent.trim();
//         let semester = row.cells[3]?.textContent.trim();
//         let subject = row.cells[4]?.textContent.trim();
//         let totalStudent = row.cells[5]?.textContent.trim();

//         if (department && course && semester && subject && totalStudent) {
//             tableData.push({ department, course, semester, subject, totalStudent });
//         }
//     });


//     if (tableData.length === 0) {
//         alert("No valid data in the table.");
//         return;
//     }

//     const form = document.createElement('form');
//     form.method = 'POST';
//     form.action = 'xyz/dev/main_activity_new.php';
//     form.target = '_blank'; // 🔥 Open the form submission in a new tab

//     const addField = (name, value) => {
//         const input = document.createElement('input');
//         input.type = 'hidden';
//         input.name = name;
//         input.value = value;
//         form.appendChild(input);
//     };

//     addField('startTime', startTime);
//     addField('benchSeat', benchSeat);
//     addField('selectedExam', selectedExam);
//     addField('enteredExamName', enteredExamName);
//     addField('startDate', startDate);
//     addField('save', 1);
//     addField('tableData', JSON.stringify(tableData));

//     document.body.appendChild(form);
//     form.submit();
// });



document.getElementById('generate').addEventListener('click', async function () {
    const startTime = document.getElementById('startTime').value.trim();
    const benchSeat = parseInt(document.getElementById('benchSeat').value.trim(), 10);
    const selectedExam = document.getElementById('examSelect').value.trim();
    const enteredExamName = document.getElementById('examInput').value.trim();
    const startDate = document.getElementById('startDate').value.trim();
    const rows = document.querySelectorAll("#tableBody tr");

    // Validation for exam selection and date
    if (selectedExam === "" && enteredExamName === "") {
        alert("Please select an exam or enter an exam name.");
        return;
    }

    if (startDate === "") {
        alert("Please select a date.");
        return;
    }

    // Validation for start time, bench seat, and rows
    if (!startTime || isNaN(benchSeat) || rows.length === 0) {
        alert("Please select a valid Start Time, Bench Seat (number), and add at least one row.");
        return;
    }

    let tableData = [];
    rows.forEach(row => {
        let department = row.cells[1]?.getAttribute('data-department') || row.cells[1]?.textContent.trim();
        let course = row.cells[2]?.getAttribute('data-course') || row.cells[2]?.textContent.trim();
        let semester = row.cells[3]?.getAttribute('data-semester') || row.cells[3]?.textContent.trim();
        let subject = row.cells[4]?.getAttribute('data-subject') || row.cells[4]?.textContent.trim();  // Added subject extraction
        let totalStudent = parseInt(row.cells[5]?.getAttribute('data-totalStudent') || row.cells[5]?.textContent.trim(), 10);

        // If all required fields are present and totalStudent is a valid number
        if (department && course && semester && subject && !isNaN(totalStudent)) {
            tableData.push({ department, course, semester, subject, totalStudent });  // Added subject to the table data
        }
    });

    // If no valid rows are found, show error
    if (tableData.length === 0) {
        alert("No valid data found in the table.");
        return;
    }

    // Ask user to confirm save
    const userChoice = confirm("Do you want to save this data to the database? Press OK for YES, Cancel for NO.");
    const save = userChoice ? 1 : 0;

    try {
        const response = await fetch('xyz/dev/main_activity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                startTime, 
                benchSeat, 
                selectedExam, 
                enteredExamName, 
                startDate, 
                tableData,
                save 
            }),
        });

        if (!response.ok) {
            throw new Error(`Server error: ${response.statusText}`);
        }

        const responseData = await response.text();

        if (save === 1) {
            // If user clicked YES (save)
            alert("Successfully saved to database!");

            // Optional: Then show your modal
            document.getElementById('modalBodyContent').innerHTML = responseData;
            new bootstrap.Modal(document.getElementById('generatedDataModal')).show();

        } else {
            // If user clicked NO (not save)
            document.getElementById('modalBodyContent').innerHTML = responseData;
            new bootstrap.Modal(document.getElementById('generatedDataModal')).show();
        }

    } catch (error) {
        console.error("Fetch Error:", error);
        alert("An error occurred while processing the request. Please try again.");
    }
});



// document.getElementById('generate').addEventListener('click', async function () {
//     const startTime = document.getElementById('startTime').value.trim();
//     const benchSeat = parseInt(document.getElementById('benchSeat').value.trim(), 10);
//     const selectedExam = document.getElementById('examSelect').value.trim();
//     const enteredExamName = document.getElementById('examInput').value.trim();
//     const startDate = document.getElementById('startDate').value.trim();
//     const rows = document.querySelectorAll("#tableBody tr");

//     // Validation for exam selection and date
//     if (selectedExam === "" && enteredExamName === "") {
//         alert("Please select an exam or enter an exam name.");
//         return;
//     }

//     if (startDate === "") {
//         alert("Please select a date.");
//         return;
//     }

//     // Validation for start time, bench seat, and rows
//     if (!startTime || isNaN(benchSeat) || rows.length === 0) {
//         alert("Please select a valid Start Time, Bench Seat (number), and add at least one row.");
//         return;
//     }

//     let tableData = [];
//     rows.forEach(row => {
//         let department = row.cells[1]?.getAttribute('data-department') || row.cells[1]?.textContent.trim();
//         let course = row.cells[2]?.getAttribute('data-course') || row.cells[2]?.textContent.trim();
//         let semester = row.cells[3]?.getAttribute('data-semester') || row.cells[3]?.textContent.trim();
//         let subject = row.cells[4]?.getAttribute('data-subject') || row.cells[4]?.textContent.trim();  // Added subject extraction
//         let totalStudent = parseInt(row.cells[5]?.getAttribute('data-totalStudent') || row.cells[5]?.textContent.trim(), 10);

//         // If all required fields are present and totalStudent is a valid number
//         if (department && course && semester && subject && !isNaN(totalStudent)) {
//             tableData.push({ department, course, semester, subject, totalStudent });  // Added subject to the table data
//         }
//     });

//     // If no valid rows are found, show error
//     if (tableData.length === 0) {
//         alert("No valid data found in the table.");
//         return;
//     }

//     // Ask user to confirm save
//     const userChoice = confirm("Do you want to save this data to the database? Press OK for YES, Cancel for NO.");
//     const save = userChoice ? 1 : 0;

//     try {
//         const response = await fetch('xyz/dev/main_activity.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({ 
//                 startTime, 
//                 benchSeat, 
//                 selectedExam, 
//                 enteredExamName, 
//                 startDate, 
//                 tableData,
//                 save 
//             }),
//         });

//         if (!response.ok) {
//             throw new Error(`Server error: ${response.statusText}`);
//         }

//         const responseData = await response.text();

//         if (save === 1) {
//             // If user clicked YES (save)
//             alert("Successfully saved to database!");

//             // Redirect to a new blank page after saving
//             window.open('xyz/dev/print-hall-ticket.php', '_blank'); // Open in a new blank tab or window

//         } else {
//             // If user clicked NO (not save)
//             // alert("Data not saved!");

//             // Redirect to a different page if not saving
//             window.open('xyz/dev/print-hall-ticket.php', '_blank'); // Open in a new blank tab or window
//         }

//     } catch (error) {
//         console.error("Fetch Error:", error);
//         alert("An error occurred while processing the request. Please try again.");
//     }
// });


// ok
// let rowCount = 1;

// function addRow() {
//     const departmentSelect = document.querySelector('select[name="department[]"]');
//     const courseSelect = document.querySelector('select[name="course[]"]');
//     const semesterSelect = document.querySelector('select[name="semester[]"]');
//     const subjectSelect = document.querySelector('select[name="subject[]"]');

//     if (!departmentSelect || !courseSelect || !semesterSelect || !subjectSelect) {
//         alert('Dropdown elements are missing!');
//         return;
//     }

//     const department = departmentSelect.value.trim();
//     const departmentText = departmentSelect.options[departmentSelect.selectedIndex].text;

//     const course = courseSelect.value.trim();
//     const courseText = courseSelect.options[courseSelect.selectedIndex].text;

//     const semester = semesterSelect.value.trim();
//     const semesterText = semesterSelect.options[semesterSelect.selectedIndex].text;

//     const subject = subjectSelect.value.trim();
//     const subjectText = subjectSelect.options[subjectSelect.selectedIndex].text;

//     if (!department || !course || !semester || !subject) {
//         alert('Please select all fields!');
//         return;
//     }

//     // Prevent duplicate entries
//     const existingRows = document.querySelectorAll("#tableBody tr");
//     for (let row of existingRows) {
//         const existingDepartment = row.cells[1].getAttribute('data-department');
//         const existingCourse = row.cells[2].getAttribute('data-course');
//         const existingSemester = row.cells[3].getAttribute('data-semester');
//         const existingSubject = row.cells[5].getAttribute('data-subject');

//         if (existingDepartment === department && existingCourse === course && existingSemester === semester && existingSubject === subject) {
//             alert("This combination already exists in the table.");
//             return;
//         }
//     }

//     // Create an AJAX request to fetch total students
//     const xhr = new XMLHttpRequest();
//     xhr.open('POST', 'procs/getTotalStudents.php', true);
//     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

//     xhr.onreadystatechange = function () {
//         if (xhr.readyState === 4) {
//             if (xhr.status === 200) {
//                 try {
//                     const data = JSON.parse(xhr.responseText);

//                     if (data.error) {
//                         console.error('Error:', data.error);
//                         alert('Error fetching student count: ' + data.error);
//                         return;
//                     }

//                     const totalStudents = data.total ?? 0; // Default to 0 if not provided

//                     // Insert row into the table
//                     const tableBody = document.getElementById('tableBody');
//                     const newRow = document.createElement('tr');

//                     newRow.innerHTML = `
//                         <td>${rowCount}</td>
//                         <td data-department="${department}">${departmentText}</td>
//                         <td data-course="${course}">${courseText}</td>
//                         <td data-semester="${semester}">${semesterText}</td>
//                         <td data-subject="${subject}">${subjectText}</td>
//                         <td data-totalStudent="${totalStudents}">${totalStudents}</td>
//                         <td>
//                             <button class="btn btn-transparent" style="background: none; border: none;" onclick="deleteRow(this)" >
//                                 <i class="fad fa-file-times"></i> remove
//                             </button>
//                         </td>
//                     `;

//                     tableBody.appendChild(newRow);
//                     rowCount++;
//                 } catch (error) {
//                     console.error("JSON Parsing Error:", error);
//                     alert("An error occurred while processing the response.");
//                 }
//             } else {
//                 console.error("AJAX Error:", xhr.statusText);
//                 alert("An error occurred while fetching student count.");
//             }
//         }
//     };

//     // Send data to get total students
//     const params = `department=${encodeURIComponent(department)}&course=${encodeURIComponent(course)}&semester=${encodeURIComponent(semester)}&subject=${encodeURIComponent(subject)}`;
//     xhr.send(params);
// }
let rowCount = 1; // Global row count

function addRow() {
    // Select dropdowns for department, course, semester, and subject
    const departmentSelect = document.querySelector('select[name="department[]"]');
    const courseSelect = document.querySelector('select[name="course[]"]');
    const semesterSelect = document.querySelector('select[name="semester[]"]');
    const subjectSelect = document.querySelector('select[name="subject[]"]');

    if (!departmentSelect || !courseSelect || !semesterSelect || !subjectSelect) {
        alert('Dropdown elements are missing!');
        return;
    }

    const department = departmentSelect.value.trim();
    const departmentText = departmentSelect.options[departmentSelect.selectedIndex].text;

    const course = courseSelect.value.trim();
    const courseText = courseSelect.options[courseSelect.selectedIndex].text;

    const semester = semesterSelect.value.trim();
    const semesterText = semesterSelect.options[semesterSelect.selectedIndex].text;

    const subject = subjectSelect.value.trim();
    const subjectText = subjectSelect.options[subjectSelect.selectedIndex].text;

    // Check if all fields are selected
    if (!department || !course || !semester || !subject) {
        alert('Please select all fields!');
        return;
    }

    // Prevent duplicate entries
    const existingRows = document.querySelectorAll("#tableBody tr");
    for (let row of existingRows) {
        const existingDepartment = row.cells[1].getAttribute('data-department');
        const existingCourse = row.cells[2].getAttribute('data-course');
        const existingSemester = row.cells[3].getAttribute('data-semester');
        const existingSubject = row.cells[4].getAttribute('data-subject');

        // If the combination of department, course, semester, and subject already exists
        if (existingDepartment === department && existingCourse === course && existingSemester === semester && existingSubject === subject) {
            alert("This combination already exists in the table.");
            return;
        }
    }

    // AJAX request to fetch total students
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'procs/getTotalStudents.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);

                    if (data.error) {
                        console.error('Error:', data.error);
                        alert('Error fetching student count: ' + data.error);
                        return;
                    }

                    const totalStudents = data.total ?? 0; // Default to 0 if not provided

                    // Insert row into the table
                    const tableBody = document.getElementById('tableBody');
                    const newRow = document.createElement('tr');

                    newRow.innerHTML = `
                        <td>${rowCount}</td>
                        <td data-department="${department}">${departmentText}</td>
                        <td data-course="${course}">${courseText}</td>
                        <td data-semester="${semester}">${semesterText}</td>
                        <td data-subject="${subject}">${subjectText}</td>
                        <td data-totalStudent="${totalStudents}">${totalStudents}</td>
                        <td>
                            <button class="btn btn-transparent" style="background: none; border: none;" onclick="deleteRow(this)">
                                <i class="fad fa-file-times"></i> remove
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(newRow);
                    rowCount++; // Increment row count after adding the new row
                } catch (error) {
                    console.error("JSON Parsing Error:", error);
                    alert("An error occurred while processing the response.");
                }
            } else {
                console.error("AJAX Error:", xhr.statusText);
                alert("An error occurred while fetching student count.");
            }
        }
    };

    // Send data to get total students
    const params = `department=${encodeURIComponent(department)}&course=${encodeURIComponent(course)}&semester=${encodeURIComponent(semester)}&subject=${encodeURIComponent(subject)}`;
    xhr.send(params);
}

</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchDepartments();
});

function fetchDepartments() {
    fetch("procs/fetch_departments.php")
        .then(response => response.json())
        .then(data => {
            let dropdown = document.getElementById("departmentDropdown");
            data.forEach(dept => {
                let option = document.createElement("option");
                option.value = dept.department_id;
                option.textContent = dept.department_name;
                dropdown.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching departments:", error));
}

function fetchCoursesAndSemesters(selectElement) {
    var departmentId = selectElement.value;
    var row = selectElement.closest('.fieldGroup');
    
    if (departmentId) {
        fetch('procs/fetch_courses_semesters.php?department_id=' + departmentId)
        .then(response => response.json())
        .then(data => {
            var courseDropdown = row.querySelector('.courseSelect');
            var semesterDropdown = row.querySelector('.semesterSelect');
            var subjectDropdown = row.querySelector('.subjectSelect');
            
            courseDropdown.innerHTML = '<option value="">Select Course</option>';
            semesterDropdown.innerHTML = '<option value="">Select Semester</option>';
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';

            data.courses.forEach(course => {
                courseDropdown.innerHTML += `<option value="${course.id}">${course.name}</option>`;
            });

            data.semesters.forEach(semester => {
                semesterDropdown.innerHTML += `<option value="${semester}">Semester ${semester}</option>`;
            });

            // Fetch subjects once courses are selected
            if (courseDropdown.value && semesterDropdown.value) {
                fetchSubjects(departmentId, courseDropdown.value, semesterDropdown.value, subjectDropdown);
            }

            // Add event listener to fetch subjects when course or semester changes
            courseDropdown.addEventListener('change', function() {
                fetchSubjects(departmentId, courseDropdown.value, semesterDropdown.value, subjectDropdown);
            });

            semesterDropdown.addEventListener('change', function() {
                fetchSubjects(departmentId, courseDropdown.value, semesterDropdown.value, subjectDropdown);
            });
        })
        .catch(error => console.error('Error fetching courses and semesters:', error));
    }
}

function fetchSubjects(departmentId, courseId, semester, subjectDropdown) {
    if (courseId && semester) {
        fetch(`procs/ex_get_subject.php?department_id=${departmentId}&course_id=${courseId}&semester_id=${semester}`)
        .then(response => response.json())
        .then(data => {
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';

            if (data.subjects && data.subjects.length) {
                data.subjects.forEach(subject => {
                    subjectDropdown.innerHTML += `<option value="${subject.subject_code}">${subject.subject}</option>`;
                });
            } else {
                subjectDropdown.innerHTML = '<option value="">No subjects available</option>';
            }
        })
        .catch(error => console.error('Error fetching subjects:', error));
    }
}


</script>

<script>
// Function to handle selection from the dropdown
function selectExam() {
    const examSelect = document.getElementById('examSelect');
    console.log("Selected exam:", examSelect.value);
}

// Function to handle input field changes
function selectInput() {
    const examInput = document.getElementById('examInput');
    console.log("Entered exam name:", examInput.value);
}

// Function to toggle between enabling and disabling the dropdown and input
function toggleSelection() {
    const examSelectContainer = document.getElementById('examSelectContainer');
    const examInputContainer = document.getElementById('examInputContainer');
    const toggleButton = document.getElementById('toggleButton');
    const examSelect = document.getElementById('examSelect');
    const examInput = document.getElementById('examInput');

    // Check if dropdown is currently enabled or disabled
    if (examSelectContainer.style.display === "none") {
        // Enable the dropdown and disable the text input
        examSelectContainer.style.display = "block";
        examInputContainer.style.display = "none";
        examSelect.disabled = false;
        examInput.disabled = true;
        toggleButton.textContent = "Switch to Exam Name";
    } else {
        // Enable the text input and disable the dropdown
        examSelectContainer.style.display = "none";
        examInputContainer.style.display = "block";
        examSelect.disabled = true;
        examInput.disabled = false;
        toggleButton.textContent = "Switch to select exam";
    }
}    
</script>
<?php include 'includes/footer.php'; ?>
