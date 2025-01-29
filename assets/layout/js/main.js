// $(document).ready(function () {
//     // Click handler for module selection
//     $("#triggerModuleSelector").click(function () {
//         var selectedModuleSelector = $("#moduleSelector").val();
//         if (selectedModuleSelector === "semesterExam") {
//             $("#outputModuleSelector").html(`
//                 <label for="selectDetails">Add Details:</label>
//                 <select id="selectDetails">
//                     <option value="room">Room</option>
//                     <option value="department">Department</option>
//                     <option value="faculty">Faculty</option>
//                     <option value="student">Student</option>
//                     <option value="enquiry">Enquiry</option>
//                 </select>
//                 <button type="button" id="triggerSelectDetails">Submit</button>

//                 <div class="container text-light">
//                     <p>Select Date and Time</p>
//                     <form>
//                         <div class="mb-3">
//                             <label for="date">Select Date:</label>
//                             <input type="date" id="date" name="date">
//                         </div>

//                         <div class="mb-3">
//                             <label for="startTime">Start Time:</label>
//                             <input type="time" id="startTime" name="startTime">
//                         </div>

//                         <div class="mb-3">
//                             <label for="endTime">End Time:</label>
//                             <input type="time" id="endTime" name="endTime">
//                         </div>

//                         <button type="submit">Save</button>
//                     </form>

//                     <p>Include Exam Department:</p>
//                     <button type="button" data-bs-toggle="modal" data-bs-target="#selectDepartment" data-bs-whatever="@mdo">Add</button>

//                     <p>Generate Exam Room Seat:</p>
//                     <button type="button">Auto</button>
//                     <button type="button">Manual</button>
//                 </div>
//             `);
//         }
//     });

//     // Use event delegation for dynamically added elements
//     $(document).on("click", "#triggerSelectDetails", function () {
//         // Get the selected option
//         var selectedOption = $("#selectDetails").val();

//         // Display dynamic content based on the selected option
//         if (selectedOption === "room") {
//             $("#demo").html(`
//                 <h2>Room Details</h2>
//                 <p>Each room is equipped with the necessary facilities to conduct exams smoothly. Here are the room features:</p>
//                 <ul>
//                     <li>Seating capacity: 50</li>
//                     <li>Whiteboards and markers</li>
//                     <li>Projector available</li>
//                 </ul>
//             `);
//         } else if (selectedOption === "department") {
//             $("#demo").html(`
//                 <h2>Department Details</h2>
//                 <p>Our university has the following departments:</p>
//                 <ul>
//                     <li>Computer Science</li>
//                     <li>Mechanical Engineering</li>
//                     <li>Civil Engineering</li>
//                     <li>Electronics and Communication</li>
//                 </ul>
//             `);
//         } else if (selectedOption === "faculty") {
//             $("#demo").html(`
//                 <h2>Faculty Details</h2>
//                 <p>Our faculty members include:</p>
//                 <ul>
//                     <li>Dr. John Smith - Computer Science</li>
//                     <li>Dr. Jane Doe - Mechanical Engineering</li>
//                     <li>Dr. Emily Brown - Civil Engineering</li>
//                     <li>Dr. Michael Taylor - Electronics</li>
//                 </ul>
//             `);
//         } else if (selectedOption === "student") {
//             $("#demo").html(`
//                 <h2>Student Details</h2>
//                 <p>Our student body is diverse, including:</p>
//                 <ul>
//                     <li>Undergraduate Students</li>
//                     <li>Postgraduate Students</li>
//                     <li>International Students</li>
//                 </ul>
//             `);
//         } else if (selectedOption === "enquiry") {
//             $("#demo").html(`
//                 <h2>Enquiry Details</h2>
//                 <p>If you have any questions or concerns, please contact us:</p>
//                 <ul>
//                     <li>Email: support@university.com</li>
//                     <li>Phone: +1 234 567 890</li>
//                     <li>Office Hours: 9 AM - 5 PM</li>
//                 </ul>
//             `);
//         }
//     });
// });



$(document).ready(function () {
    // Populate the module details when the module selector is triggered
    $("#triggerModuleSelector").click(function () {
        var selectedModuleSelector = $("#moduleSelector").val();
        if (selectedModuleSelector == "default") {
            $("#output2").html(`
            <div class="container d-flex justify-content-center align-items-center vh-100">
                <img src="assets/images/siblogomain.png" alt="Centered Image" class="img-fluid opacity-25">
            </div>
            `);
        }
        else if (selectedModuleSelector === "semesterExam") {

            $("#outputModuleSelector").html(`
                <div class="container">
                    <label for="selectDetails" class="w-100">Add Details:</label>
                    <select id="selectDetails" class="">
                        <option value="default">Default</option>
                        <option value="room">Room</option>
                        <option value="department">Department</option>
                        <option value="faculty">Faculty</option>
                        <option value="student">Student</option>
                        <option value="enquiry">Enquiry</option>
                    </select>
                    <button type="button" id="triggerSelectDetails" class="">Submit</button>      
                </div>
                <div class="container mt-3">
                    <label for="date" class="w-100">Select Date:</label>
                    <input type="date" id="date" name="date">
                    <button type="submit">Save</button>
                </div>
                <!-- Include Exam Department Section -->
                <div class="container mt-3">
                    <p>Include Exam Department:</p>
                    <button type="button" class="" data-bs-toggle="modal" data-bs-target="#selectDepartmentModal">Add</button>
                </div>

                <div class="container">
                    <p>Generate Exam Room Seat:</p>
                    <button type="button">Auto</button>
                    <button type="button">Manual</button>
                </div>                        

            `);
            $("#output2").html(`
            <!-- Room Sections -->
            <div class="container mt-3">
                <p class="text-uppercase"><i class="bi bi-house-check"></i> Selected Room and Hall</p>
                <hr>
                <div class="row no-gutters">
                    <div class="col-lg-3">
                        <!-- <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Depertment Sections -->
            <div class="container mt-3">
                <p class="text-uppercase"><i class="bi bi-house-check"></i> Selected Departments and Exam Times</p>
                <hr>
                <div class="row no-gutters">
                    <div class="col-lg-3">
                        <!-- Department and Exam Time List -->
                        <div class="container mt-4">
                            <!-- <h4></h4> -->
                            <ul id="departmentList" class="list-group">
                                <!-- Departments will be added here dynamically -->
                            </ul>
                        </div>
                        <!-- <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Faculty Sections -->
            <div class="container mt-3">
                <p class="text-uppercase"><i class="bi bi-house-check"></i> Selected Faculty</p>
                <hr>
                <div class="row no-gutters">
                    <div class="col-lg-3">
                        <!-- <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- Student Sections -->
            <div class="container mt-3">
                <p class="text-uppercase"><i class="bi bi-house-check"></i> Selected Available Students</p>
                <hr>
                <div class="row no-gutters">
                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>


                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>                    
                    <div class="col-lg-3">
                        <div class="dep-card">
                            <div class="dep-content">
                                <p class="text-uppercase">room: lahoti 1</p>
                                <h4>SEAT AVL<b> 30</b></h4>
                                <p>Master of computer application (data science)</p>
                                <div class="dep-option">
                                    <button type="button">view seat</button>
                                    <button type="button">Actions</button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#download_report"
                                        data-bs-whatever="@mdo">download report</button>
                                </div>
                            </div>
                        </div> 
                    </div>
                    
                </div>
            </div>
            `);
        }
    });

    
    // Use event delegation for dynamically created #triggerSelectDetails
    $(document).on("click", "#triggerSelectDetails", function () {
        // Get the selected option
        var selectedOption = $("#selectDetails").val();

        // Prepare the modal content based on the selected option
        let modalContent = "";
        if (selectedOption === "room") {
            modalContent = `
                <h2>Room Details</h2>
                <p>Each room is equipped with the necessary facilities to conduct exams smoothly. Here are the room features:</p>
                <ul>
                    <li>Seating capacity: 50</li>
                    <li>Whiteboards and markers</li>
                    <li>Projector available</li>
                </ul>
            `;
        } else if (selectedOption === "department") {
            modalContent = `
                <h2>Department Details</h2>
                <p>Our university has the following departments:</p>
                <ul>
                    <li>Computer Science</li>
                    <li>Mechanical Engineering</li>
                    <li>Civil Engineering</li>
                    <li>Electronics and Communication</li>
                </ul>
            `;
        } else if (selectedOption === "faculty") {
            modalContent = `
                <h2>Faculty Details</h2>
                <p>Our faculty members include:</p>
                <ul>
                    <li>Dr. John Smith - Computer Science</li>
                    <li>Dr. Jane Doe - Mechanical Engineering</li>
                    <li>Dr. Emily Brown - Civil Engineering</li>
                    <li>Dr. Michael Taylor - Electronics</li>
                </ul>
            `;
        } else if (selectedOption === "student") {
            modalContent = `
                <h2>Student Details</h2>
                <p>Our student body is diverse, including:</p>
                <ul>
                    <li>Undergraduate Students</li>
                    <li>Postgraduate Students</li>
                    <li>International Students</li>
                </ul>
            `;
        } else if (selectedOption === "enquiry") {
            modalContent = `
                <h2>Enquiry Details</h2>
                <p>If you have any questions or concerns, please contact us:</p>
                <ul>
                    <li>Email: support@university.com</li>
                    <li>Phone: +1 234 567 890</li>
                    <li>Office Hours: 9 AM - 5 PM</li>
                </ul>
            `;
        } else {
            modalContent = "<p>Please select a valid option.</p>";
        }

        // Inject content into the modal and show the modal
        $("#modalContent").html(modalContent);
        $("#contentModal").modal("show");
    });
});



// Array to store added department and exam time combinations
let addedData = [];

// Handle the "Save" button click in the modal
document.getElementById('saveDepartmentButton').addEventListener('click', function () {
    var department = document.getElementById('departmentSelect').value;
    var examStartTime = document.getElementById('examStartTime').value;
    var examEndTime = document.getElementById('examEndTime').value;

    // Initially hide error messages
    document.getElementById('departmentError').style.display = 'none';
    document.getElementById('startTimeError').style.display = 'none';
    document.getElementById('endTimeError').style.display = 'none';
    document.getElementById('duplicateError').style.display = 'none';

    // Validate inputs
    if (!department || !examStartTime || !examEndTime) {
        // Show error messages if validation fails
        if (!department) {
            document.getElementById('departmentError').style.display = 'block';
        }
        if (!examStartTime) {
            document.getElementById('startTimeError').style.display = 'block';
        }
        if (!examEndTime) {
            document.getElementById('endTimeError').style.display = 'block';
        }
        return; // Prevent the modal from closing
    }

    // Check if the department is already added
    let duplicateFound = addedData.some(function (item) {
        return item.department === department;
    });

    if (duplicateFound) {
        // Show duplicate error if the department is already added
        document.getElementById('duplicateError').style.display = 'block';
        return; // Prevent the modal from closing
    }

    // Add the new data to the addedData array
    addedData.push({
        department: department,
        startTime: examStartTime,
        endTime: examEndTime
    });

    // Create a new list item for the department and exam times
    var listItem = document.createElement('li');
    listItem.classList.add('list-group-item');
    listItem.innerHTML = `
        <strong>Department:</strong> ${department} <br>
        <strong>Exam Start Time:</strong> ${examStartTime} <br>
        <strong>Exam End Time:</strong> ${examEndTime}
        <button class="btn btn-warning btn-sm float-end ml-2" onclick="editData(this)">Edit</button>
        <button class="btn btn-danger btn-sm float-end" onclick="removeData(this)">Remove</button>
    `;

    // Add the new item to the department list
    document.getElementById('departmentList').appendChild(listItem);

    // Close the modal programmatically
    var modal = bootstrap.Modal.getInstance(document.getElementById('selectDepartmentModal'));
    modal.hide();

    // Reset the form inputs after closing the modal
    document.getElementById('examForm').reset();
});

// Function to remove data
function removeData(button) {
    // Get the list item that contains the remove button
    var listItem = button.parentElement;
    
    // Remove the list item from the DOM
    listItem.remove();

    // Remove the corresponding data from the addedData array
    var department = listItem.querySelector('strong').textContent.split(': ')[1];
    var startTime = listItem.querySelectorAll('strong')[1].textContent.split(': ')[1];
    var endTime = listItem.querySelectorAll('strong')[2].textContent.split(': ')[1];

    addedData = addedData.filter(function (item) {
        return !(item.department === department && item.startTime === startTime && item.endTime === endTime);
    });
}

// Function to edit data
function editData(button) {
    // Get the list item that contains the edit button
    var listItem = button.parentElement;
    
    // Extract the current data from the list item
    var department = listItem.querySelector('strong').textContent.split(': ')[1];
    var startTime = listItem.querySelectorAll('strong')[1].textContent.split(': ')[1];
    var endTime = listItem.querySelectorAll('strong')[2].textContent.split(': ')[1];

    // Set the values in the modal form
    document.getElementById('departmentSelect').value = department;
    document.getElementById('examStartTime').value = startTime;
    document.getElementById('examEndTime').value = endTime;

    // Remove the item from the addedData array (so we can update it later)
    addedData = addedData.filter(function (item) {
        return !(item.department === department && item.startTime === startTime && item.endTime === endTime);
    });

    // Open the modal
    var modal = new bootstrap.Modal(document.getElementById('selectDepartmentModal'));
    modal.show();
}