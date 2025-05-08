<?php 
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mb-5 bg-light vh-100">
    <div class="row justify-content-center align-items-center h-25">
        <!-- Exam Date -->
        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label for="examDateSelect" class="form-label">Select Exam Date:</label>
                <select class="form-select" id="examDateSelect">
                    <option selected disabled>Loading dates...</option>
                </select>
            </div>
        </div>

        <!-- Exam Event (Time) -->
        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label for="examEventSelect" class="form-label">Select Event:</label>
                <select class="form-select" id="examEventSelect" disabled>
                    <option selected disabled>Select a date first</option>
                </select>
            </div>
        </div>

        <!-- Department List -->
        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label for="departmentSelect" class="form-label">Select Department:</label>
                <select class="form-select" id="departmentSelect" disabled>
                    <option selected disabled>Select department</option>
                </select>
            </div>
        </div>

        <!-- Show Button -->
        <div class="col-md-2 col-12 mt-3 d-flex justify-content-center align-items-end">
            <div class="container p-2  w-100">
                <button class="btn btn-primary w-100 btn-md" id="printButton"><i class="far fa-plus"></i> CREATE</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dateSelect = document.getElementById("examDateSelect");
    const eventSelect = document.getElementById("examEventSelect");
    const departmentSelect = document.getElementById("departmentSelect");
    const printButton = document.getElementById("printButton");

    // Load exam dates
    fetch('xyz/api/get_exam_dates.php')
        .then(res => res.json())
        .then(data => {
            dateSelect.innerHTML = '<option selected disabled>Select a date</option>';
            data.forEach(item => {
                dateSelect.innerHTML += `<option value="${item.date}">${item.date}</option>`;
            });
            dateSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading dates:', error);
            dateSelect.innerHTML = '<option selected disabled>Error loading dates</option>';
        });

    // Load events when a date is selected
    dateSelect.addEventListener("change", function () {
        const selectedDate = this.value;
        eventSelect.innerHTML = '<option selected disabled>Loading events...</option>';
        eventSelect.disabled = true;
        departmentSelect.innerHTML = '<option selected disabled>Select department</option>';
        departmentSelect.disabled = true;

        fetch(`xyz/api/get_events_by_date.php?date=${selectedDate}`)
            .then(res => res.json())
            .then(data => {
                eventSelect.innerHTML = '<option selected disabled>Select an event</option>';
                data.forEach(item => {
                    let formattedTime = new Date(`1970-01-01T${item.time}`).toLocaleTimeString([], {
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                    eventSelect.innerHTML += `<option value="${item.time}">${formattedTime} (${item.session})</option>`;
                });
                eventSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading events:', error);
                eventSelect.innerHTML = '<option selected disabled>Error loading events</option>';
            });
    });

    // Load departments when an event is selected
    eventSelect.addEventListener("change", function () {
        fetchDepartments();
    });

    function fetchDepartments() {
        const selectedDate = dateSelect.value;
        const selectedTime = eventSelect.value;

        // Clear previous results
        departmentSelect.innerHTML = '<option selected disabled>Loading departments...</option>';
        departmentSelect.disabled = true;

        // Fetch departments from the server
        fetch(`xyz/api/get_departments_by_datetime.php?date=${selectedDate}&time=${selectedTime}`)
            .then(res => res.json())
            .then(data => {
                departmentSelect.innerHTML = '<option selected disabled>Select a department</option>';
                data.forEach(department => {
                    departmentSelect.innerHTML += `<option value="${department.department_id}">${department.department_name}</option>`;
                });
                departmentSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading departments:', error);
                departmentSelect.innerHTML = '<option selected disabled>Error loading departments</option>';
            });
    }

    // Redirect to attendance-view.php when clicking the create button
    printButton.addEventListener("click", function () {
        const selectedDate = dateSelect.value;
        const selectedTime = eventSelect.value;
        const selectedDepartment = departmentSelect.value;

        if (!selectedDate || !selectedTime || !selectedDepartment) {
            alert("Please select a date, event, and department first.");
            return;
        }

        const url = `create-top-sheet.php?date=${encodeURIComponent(selectedDate)}&time=${encodeURIComponent(selectedTime)}&department_id=${encodeURIComponent(selectedDepartment)}`;
        window.open(url, '_blank');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
