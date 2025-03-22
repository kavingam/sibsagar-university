<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row g-0">
        <div class="col-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Exam Seat plan</h4>
        </div>
        <div class="col-3">
            <div class="container p-3">
                <label for="startTime" class="form-label">Exam Start Time:</label>
                <input type="time" class="form-control" id="startTime" name="startTime">
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
                <button type="button" class="btn btn-success btn-sm" id="generate">Generate</button>
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
            <div class="container p-2 border border-end-0">
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
            <div class="container p-2 border border-start-0 border-end-0">
                <label>Course:</label>
                <select name="course[]" class="form-control courseSelect">
                    <option value="">Select Course</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="container p-2 border border-start-0">
                <label>Semester:</label>
                <select name="semester[]" class="form-control semesterSelect">
                    <option value="">Select Semester</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="container d-flex justify-content-center align-items-center mt-1">
                <div class="text-center">
                    <button type="button" class="btn btn-success btn-sm w-100 mb-2" onclick="addRow()">Add Exam</button>
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeRow()">Remove</button>
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

<!-- Full-Screen Modal -->
<div class="modal fade" id="generatedDataModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-center">Generate Seat Plan / Download Report </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalBodyContent">
                    <!-- PHP returned content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>
                <button class="btn btn-secondary" onclick="printContent()">Print</button>
                <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
</script>
<script>
    function printContent() {
    let printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(document.getElementById("modalBodyContent").innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// function downloadPDF() {
//     const element = document.getElementById("modalBodyContent");
//     html2pdf(element);
// }

// function downloadPDF() {
//     const element = document.getElementById("modalBodyContent");

//     const options = {
//         margin: 10,
//         filename: 'document.pdf',
//         image: { type: 'jpeg', quality: 1 },  // Highest image quality
//         html2canvas: { scale: 3 },  // Higher scale for better quality
//         jsPDF: { 
//             unit: 'mm', 
//             format: 'a4',  // Use 'a3' or 'legal' if needed
//             orientation: 'portrait'  // Change to 'landscape' if required
//         }
//     };

//     html2pdf().set(options).from(element).save();
// }

function downloadPDF() {
    const element = document.getElementById("modalBodyContent");
    
    if (!element) {
        console.error("Element with ID 'modalBodyContent' not found.");
        return;
    }

    const options = {
        margin: 0, // Remove margins for full-page content
        filename: 'document.pdf',
        image: { type: 'jpeg', quality: 1 }, // Highest image quality
        html2canvas: {
            scale: 3, // Higher scale for better rendering without reducing content size
            useCORS: true, // To handle cross-origin images
            logging: false, // Reduce console clutter
        },
        jsPDF: {
            unit: 'mm',
            format: 'letter', // Letter size format
            orientation: 'portrait' // Keeping Portrait layout
        }
    };
    
    // Apply custom styles to enhance rendering
    element.style.backgroundColor = "#ffffff";
    
    html2pdf()
        .set(options)
        .from(element)
        .toPdf()
        .get('pdf')
        .then(function (pdf) {
            let totalPages = pdf.internal.getNumberOfPages();
            pdf.setFontSize(10); // Set text size to 10 pixels correctly
            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                pdf.text(`Page ${i} of ${totalPages}`, 10, pdf.internal.pageSize.height - 10);
            }
            console.log("PDF Generated Successfully");
        })
        .save();
}



</script>

<!-- JavaScript -->
<script>
/*
let rowCount = 1;

function addRow() {
    const departmentSelect = document.querySelector('select[name="department[]"]');
    const courseSelect = document.querySelector('select[name="course[]"]');
    const semesterSelect = document.querySelector('select[name="semester[]"]');

    const department = departmentSelect ? departmentSelect.value : "";
    const departmentText = departmentSelect ? departmentSelect.options[departmentSelect.selectedIndex].text : "";

    const course = courseSelect ? courseSelect.value : "";
    const courseText = courseSelect ? courseSelect.options[courseSelect.selectedIndex].text : "";

    const semester = semesterSelect ? semesterSelect.value : "";
    const semesterText = semesterSelect ? semesterSelect.options[semesterSelect.selectedIndex].text : "";

    if (!department || !course || !semester) {
        alert('Please select all fields!');
        return;
    }

    const tableBody = document.getElementById('tableBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${rowCount}</td>
        <td id="${department}">${departmentText}</td>
        <td id="${course}">${courseText}</td>
        <td id="${semester}">${semester}</td>
        <td class="text-center">
            <button class="btn btn-transparent" style="background: none; border: none;" onclick="deleteRow(this)">
                <i class="bi bi-trash-fill text-danger" style="font-size: 1.2rem;"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(newRow);
    rowCount++;
}
*/


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
/*
v0.0
document.getElementById('generate').addEventListener('click', function () {
    const startTime = document.getElementById('startTime').value;
    const benchSeat = document.getElementById('benchSeat').value;
    const rows = document.querySelectorAll("#tableBody tr");

    if (!startTime || !benchSeat || rows.length === 0) {
        alert("Please select Start Time, Bench Seat, and add at least one row.");
        return;
    }

    let tableData = [];
    rows.forEach(row => {
        // tableData.push({
        //     department: row.cells[1].textContent,
        //     course: row.cells[2].textContent,
        //     semester: row.cells[3].textContent
        // });
        // tableData.push({
        //     department: row.cells[1].querySelector('select')?.value || row.cells[1].innerText.trim(),
        //     course: row.cells[2].querySelector('select')?.value || row.cells[2].innerText.trim(),
        //     semester: row.cells[3].querySelector('select')?.value || row.cells[3].innerText.trim()
        // });
        tableData.push({
            department: row.cells[1].getAttribute('id'), // Get ID instead of text
            course: row.cells[2].getAttribute('id'),
            semester: row.cells[3].getAttribute('id')
        });
    });

    fetch('process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ startTime, benchSeat, tableData })
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('modalBodyContent').innerHTML = data;
        new bootstrap.Modal(document.getElementById('generatedDataModal')).show();
    });
});
*/
document.getElementById('generate').addEventListener('click', function () {
    const startTime = document.getElementById('startTime').value.trim();
    const benchSeat = document.getElementById('benchSeat').value.trim();
    const rows = document.querySelectorAll("#tableBody tr");

    if (!startTime || !benchSeat || rows.length === 0) {
        alert("Please select Start Time, Bench Seat, and add at least one row.");
        return;
    }

    let tableData = [];
    rows.forEach(row => {
        let department = row.cells[1].getAttribute('data-department') || row.cells[1].textContent.trim();
        let course = row.cells[2].getAttribute('data-course') || row.cells[2].textContent.trim();
        let semester = row.cells[3].getAttribute('data-semester') || row.cells[3].textContent.trim();

        // Ensure all values are present before adding to array
        if (department && course && semester) {
            tableData.push({ department, course, semester });
        }
    });

    if (tableData.length === 0) {
        alert("No valid data found in the table.");
        return;
    }

    // Send the data using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                document.getElementById('modalBodyContent').innerHTML = xhr.responseText;
                new bootstrap.Modal(document.getElementById('generatedDataModal')).show();
            } else {
                console.error("AJAX Error:", xhr.statusText);
                alert("An error occurred while processing the request.");
            }
        }
    };

    xhr.send(JSON.stringify({ startTime, benchSeat, tableData }));
});

let rowCount = 1;

function addRow() {
    const departmentSelect = document.querySelector('select[name="department[]"]');
    const courseSelect = document.querySelector('select[name="course[]"]');
    const semesterSelect = document.querySelector('select[name="semester[]"]');

    if (!departmentSelect || !courseSelect || !semesterSelect) {
        alert('Dropdown elements are missing!');
        return;
    }

    const department = departmentSelect.value.trim();
    const departmentText = departmentSelect.options[departmentSelect.selectedIndex].text;

    const course = courseSelect.value.trim();
    const courseText = courseSelect.options[courseSelect.selectedIndex].text;

    const semester = semesterSelect.value.trim();
    const semesterText = semesterSelect.options[semesterSelect.selectedIndex].text;

    if (!department || !course || !semester) {
        alert('Please select all fields!');
        return;
    }

    // Create an AJAX request
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

                    const tableBody = document.getElementById('tableBody');
                    const newRow = document.createElement('tr');

                    newRow.innerHTML = `
                        <td>${rowCount}</td>
                        <td data-department="${department}">${departmentText}</td>
                        <td data-course="${course}">${courseText}</td>
                        <td data-semester="${semester}">${semesterText}</td>
                        <td class="text-center">${totalStudents}</td>
                        <td class="text-center">
                            <button class="btn btn-transparent" style="background: none; border: none;" onclick="deleteRow(this)">
                                <i class="bi bi-trash-fill text-danger" style="font-size: 1.2rem;"></i>
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(newRow);
                    rowCount++;
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

    // Send data
    const params = `department=${encodeURIComponent(department)}&course=${encodeURIComponent(course)}&semester=${encodeURIComponent(semester)}`;
    xhr.send(params);
}



</script>



<script>
function fetchCoursesAndSemesters(selectElement) {
    var departmentId = selectElement.value;
    var row = selectElement.closest('.fieldGroup');
    
    if (departmentId) {
        fetch('procs/fetch_courses_semesters.php?department_id=' + departmentId)
        .then(response => response.json())
        .then(data => {
            var courseDropdown = row.querySelector('.courseSelect');
            var semesterDropdown = row.querySelector('.semesterSelect');
            
            courseDropdown.innerHTML = '<option value="">Select Course</option>';
            semesterDropdown.innerHTML = '<option value="">Select Semester</option>';

            data.courses.forEach(course => {
                courseDropdown.innerHTML += `<option value="${course.id}">${course.name}</option>`;
            });

            data.semesters.forEach(semester => {
                semesterDropdown.innerHTML += `<option value="${semester}">Semester ${semester}</option>`;
            });
        })
        .catch(error => console.error('Error fetching data:', error));
    }
}
</script>

<?php include 'includes/footer.php'; ?>
