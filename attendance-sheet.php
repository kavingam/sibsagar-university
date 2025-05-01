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

        <!-- Room List -->
        <div class="col-md-2 col-12 mb-3">
            <div class="container p-2">
                <label for="roomListSelect" class="form-label">Select Room:</label>
                <select class="form-select" id="roomListSelect" disabled>
                    <option selected disabled>Select date & event</option>
                </select>
            </div>
        </div>

        <!-- Show Button -->
        <div class="col-md-2 col-12 mt-3 d-flex justify-content-center align-items-end">
            <div class="container p-2  w-100">
                <button class="btn btn-primary w-100 btn-md" id="printButton"><i class="far fa-print"></i> PRINT</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dateSelect = document.getElementById("examDateSelect");
    const eventSelect = document.getElementById("examEventSelect");
    const roomSelect = document.getElementById("roomListSelect");
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
        .catch(error => console.error('Error loading dates:', error));

    // Load events when a date is selected
    dateSelect.addEventListener("change", function () {
        const selectedDate = this.value;
        eventSelect.innerHTML = '<option selected disabled>Loading events...</option>';
        eventSelect.disabled = true;
        roomSelect.innerHTML = '<option selected disabled>Select date & event</option>';
        roomSelect.disabled = true;

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
            .catch(error => console.error('Error loading events:', error));
    });

    // Load rooms when an event is selected
    eventSelect.addEventListener("change", function () {
        const selectedDate = dateSelect.value;
        const selectedTime = this.value;

        roomSelect.innerHTML = '<option selected disabled>Loading rooms...</option>';
        roomSelect.disabled = true;

        fetch(`xyz/api/get_rooms_by_datetime.php?date=${selectedDate}&time=${selectedTime}`)
            .then(res => res.json())
            .then(data => {
                roomSelect.innerHTML = '<option selected disabled>Select a room</option>';
                data.forEach(room => {
                    roomSelect.innerHTML += `<option value="${room.room_no}">${room.room_no} - ${room.room_name}</option>`;
                });
                roomSelect.disabled = false;
            })
            .catch(error => console.error('Error loading rooms:', error));
    });

    // Redirect to attendance-view.php
    printButton.addEventListener("click", function () {
        const selectedDate = dateSelect.value;
        const selectedTime = eventSelect.value;
        const selectedRoom = roomSelect.value;

        if (!selectedDate || !selectedTime || !selectedRoom) {
            alert("Please select a date, event, and room first.");
            return;
        }

        const url = `attendance-view.php?date=${encodeURIComponent(selectedDate)}&time=${encodeURIComponent(selectedTime)}&room_no=${encodeURIComponent(selectedRoom)}`;
        window.open(url, '_blank');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
