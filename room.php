<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row g-0 justify-content-center d-flex">
        <div class="col-lg-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Import Room</h4>
        </div>
        <div class="col-lg-3">
            <div class="container p-3">
                <label for="roomNo">Room No:</label>
                <input type="text" id="roomNo" class="form-control">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="container p-3">
                <label for="roomName">Room Name:</label>
                <input type="text" id="roomName" class="form-control">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="container p-3">
                <label for="size">Size:</label>
                <select id="size" class="form-select">
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="container p-3">
                <label for="seatCapacity">Seat Capacity:</label>
                <input type="number" id="seatCapacity" class="form-control">
            </div>
        </div>
    </div>
    <div class="container g-0 text-center my-3">
        <p>Import Room</p>
        <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("submitBtn").addEventListener("click", function () {
        let roomNo = document.getElementById("roomNo").value;
        let roomName = document.getElementById("roomName").value;
        let size = document.getElementById("size").value;
        let seatCapacity = document.getElementById("seatCapacity").value;

        let formData = new FormData();
        formData.append("roomNo", roomNo);
        formData.append("roomName", roomName);
        formData.append("size", size);
        formData.append("seatCapacity", seatCapacity);

        fetch("procs/import_room.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>


<?php include 'includes/footer.php'; ?>