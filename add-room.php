<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row g-0 justify-content-center d-flex">
        <div class="col-lg-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Add Room</h4>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="roomNo">Room No:</label>
                <input type="text" id="roomNo" class="form-control" placeholder="Enter a room serial no" required>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="p-3">
                <label for="roomName">Room Name:</label>
                <input type="text" id="roomName" class="form-control" placeholder="Enter room name" required>
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
                <label for="seatCapacity">Total Bench:</label>
                <input type="number" id="seatCapacity" class="form-control" required min="1">
            </div>
        </div>
    </div>
    <div class="text-center my-3">
        <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
        <!-- onclick="addRoom() -->
    </div>
</div>

<script>
//  JavaScript Function (AJAX Fetch)
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("submitBtn").addEventListener("click", async function () {
        let roomNo = document.getElementById("roomNo").value.trim();
        let roomName = document.getElementById("roomName").value.trim();
        let benchOrder = document.getElementById("bench_order").value;
        let seatCapacity = document.getElementById("seatCapacity").value.trim();

        // Basic validation
        if (!roomNo || !roomName || !benchOrder || !seatCapacity) {
            alert("⚠️ Please fill out all fields.");
            return;
        }

        let formData = new FormData();
        formData.append("roomNo", roomNo);
        formData.append("roomName", roomName);
        formData.append("bench_order", benchOrder);
        formData.append("seatCapacity", seatCapacity);

        try {
            let response = await fetch("xyz/space/spce_nxc.php", {
                method: "POST",
                body: formData
            });

            let result = await response.json();

            alert(result.message);
            if (result.success) {
                document.getElementById("roomNo").value = "";
                document.getElementById("roomName").value = "";
                document.getElementById("bench_order").value = "2";
                document.getElementById("seatCapacity").value = "";
            }
        } catch (error) {
            console.error("❌ Error:", error);
            alert("⚠️ An error occurred while processing your request.");
        }
    });
});

</script>

<?php include 'includes/footer.php'; ?>
