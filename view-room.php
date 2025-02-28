<?php include 'includes/header.php'; ?>
<div class="container my-4">
    <div class="row g-0 justify-content-center d-flex">
        <div class="col-lg-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Export Room</h4>
        </div>
        <div class="col-lg-12">
            <div class="container p-3">
                <table class="table table-bordered border border-secondary">
                    <thead class="text-start">
                        <tr>
                            <th>SNO</th>
                            <th>Room No</th>
                            <th>Room Name</th>
                            <th>Size</th>
                            <th>Capacity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="roomTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editRoomNo">
                <div class="mb-3">
                    <label class="form-label">Room Name:</label>
                    <input type="text" id="editRoomName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Size:</label>
                    <select id="editSize" class="form-select">
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Capacity:</label>
                    <input type="number" id="editCapacity" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateRoom()">Save Changes</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchRooms();
});

function fetchRooms() {
    fetch("procs/fetch_rooms.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("roomTableBody");
            tableBody.innerHTML = "";
            data.forEach((room, index) => {
                let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${room.room_no}</td>
                        <td>${room.room_name}</td>
                        <td>${room.size}</td>
                        <td>${room.seat_capacity}</td>
                        <td class="justify-content-center d-flex">
                            <button class="ms-2 btn btn-warning" onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.size}', '${room.seat_capacity}')">Edit</button>
                            <button class="ms-2 btn btn-danger" onclick="deleteRoom('${room.room_no}')">Delete</button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => console.error("Error fetching rooms:", error));
}

function editRoom(roomNo, roomName, size, capacity) {
    document.getElementById("editRoomNo").value = roomNo;
    document.getElementById("editRoomName").value = roomName;
    document.getElementById("editSize").value = size;
    document.getElementById("editCapacity").value = capacity;
    $("#editModal").modal("show");
}

function updateRoom() {
    let roomNo = document.getElementById("editRoomNo").value;
    let roomName = document.getElementById("editRoomName").value;
    let size = document.getElementById("editSize").value;
    let capacity = document.getElementById("editCapacity").value;

    fetch("procs/update_room.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ roomNo, roomName, size, capacity })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        $("#editModal").modal("hide");
        fetchRooms();
    });
}

function deleteRoom(roomNo) {
    if (confirm("Are you sure you want to delete this room?")) {
        fetch("procs/delete_room.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ roomNo })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            fetchRooms();
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>
