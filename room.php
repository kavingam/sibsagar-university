<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row g-0 justify-content-center d-flex">
        <div class="col-lg-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Add Room</h4>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="roomNo">Room No:</label>
                <input type="text" id="roomNo" class="form-control" placeholder="enter a room no" required>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="roomName">Room Name:</label>
                <input type="text" id="roomName" class="form-control" placeholder="enter room name" required>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="bench_order">Bench Order:</label>
                <select id="bench_order" class="form-select">
                    <option value="1">1 X 1 COLUMN</option>
                    <option value="2">1 X 2 COLUMN</option>
                    <option value="3">1 X 3 COLUMN</option>
                    <option value="4">1 X 4 COLUMN</option>
                    <option value="5">1 X 5 COLUMN</option>
                    <option value="6">1 X 6 COLUMN</option>
                    <option value="7">1 X 7 COLUMN</option>
                    <option value="8">1 X 8 COLUMN</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="seatCapacity">Seat Capacity:</label>
                <input type="number" id="seatCapacity" class="form-control" required min="1">
            </div>
        </div>
    </div>
    <div class="text-center my-3">
        <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
    </div>
</div>

<script>
$(document).ready(function () {
    $("#submitBtn").click(function () {
        let roomNo = $("#roomNo").val().trim();
        let roomName = $("#roomName").val().trim();
        let bench_order = $("#bench_order").val();
        let seatCapacity = $("#seatCapacity").val().trim();

        // Basic validation
        if (!roomNo || !roomName || !bench_order || !seatCapacity) {
            alert("Please fill out all fields.");
            return;
        }

        let formData = new FormData();
        formData.append("roomNo", roomNo);
        formData.append("roomName", roomName);
        formData.append("bench_order", bench_order);
        formData.append("seatCapacity", seatCapacity);

        $.ajax({
            url: "procs/import_room.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                alert(response.message);
                if (response.success) {
                    $("#roomNo").val("");
                    $("#roomName").val("");
                    $("#bench_order").val("2");
                    $("#seatCapacity").val("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                alert("An error occurred while processing your request.");
            }
        });
    });
});

</script>

<?php include 'includes/footer.php'; ?>
