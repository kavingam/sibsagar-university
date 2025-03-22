function fetchRooms() {
    fetch("xyz/space/spce_nxo.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("roomTableBody");
            tableBody.innerHTML = "";

            // Sorting the rooms by room_no in descending order
            // data.sort((a, b) => b.room_no.localeCompare(a.room_no, undefined, { numeric: true }));
            data.sort((a, b) => a.room_no.localeCompare(b.room_no, undefined, { numeric: true }));


            // Mapping numbers to "1 X N COLUMN" labels
            const benchOrderLabels = {
                1: "1 X 1 COLUMN",
                2: "1 X 2 COLUMN",
                3: "1 X 3 COLUMN",
                4: "1 X 4 COLUMN",
                5: "1 X 5 COLUMN",
                6: "1 X 6 COLUMN",
                7: "1 X 7 COLUMN",
                8: "1 X 8 COLUMN"
            };

            // <td>${index + 1}</td>
            data.forEach((room, index) => {
                let benchOrderText = benchOrderLabels[room.bench_order] || "Unknown"; // Default to "Unknown" if not mapped
                
                let row = `
                    <tr>
                        <td>${room.room_no}</td>
                        <td>${room.room_name}</td>
                        <td>${benchOrderText}</td>
                        <td>${room.seat_capacity}</td>
                        <td class="justify-content-center d-flex">
                            <button class="ms-2 btn" onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
                                <i class="bi bi-pencil-square text-primary"></i>
                            </button>
                            <button class="ms-2 btn" onclick="deleteRoom('${room.room_no}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => console.error("Error fetching rooms:", error));
}
// Populate the edit modal
function editRoom(roomNo, roomName, benchOrder, capacity) {
    document.getElementById("editRoomNo").value = roomNo;
    document.getElementById("editRoomName").value = decodeURIComponent(roomName);
    document.getElementById("editSize").value = benchOrder;
    document.getElementById("editCapacity").value = capacity;
    
    var editModal = new bootstrap.Modal(document.getElementById("editModal"));
    editModal.show();
}
function updateRoom() {
    let roomNo = document.getElementById("editRoomNo").value.trim();
    let roomName = document.getElementById("editRoomName").value.trim();
    let benchOrder = document.getElementById("editSize").value.trim();
    let seatCapacity = document.getElementById("editCapacity").value.trim();

    if (!roomNo || !roomName || !benchOrder || !seatCapacity) {
        alert("Please fill out all fields.");
        return;
    }

    let roomData = {
        roomNo: roomNo,
        roomName: roomName,
        benchOrder: benchOrder,
        seatCapacity: seatCapacity
    };

    fetch("xyz/space/spce_nxe.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(roomData)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while updating the room.");
    });
}

function deleteRoom(roomNo) {
    if (confirm(`Are you sure you want to delete Room No: ${roomNo}? This action cannot be undone.`)) {
        fetch("xyz/space/spce_nxr.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ roomNo })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                fetchRooms(); // Refresh the rooms list
            }
        })
        .catch(error => {
            console.error("❌ Error deleting room:", error);
            alert("⚠️ An error occurred while deleting the room.");
        });
    }
}