// function fetchRooms() {
//     fetch("xyz/space/spce_nxo.php")
//         .then(response => response.json())
//         .then(data => {
//             let tableBody = document.getElementById("roomTableBody");
//             tableBody.innerHTML = "";

//             // Sorting the rooms by room_no in descending order
//             // data.sort((a, b) => b.room_no.localeCompare(a.room_no, undefined, { numeric: true }));
//             data.sort((a, b) => a.room_no.localeCompare(b.room_no, undefined, { numeric: true }));


//             // Mapping numbers to "1 X N COLUMN" labels
//             const benchOrderLabels = {
//                 1: "1 X 1 COLUMN",
//                 2: "1 X 2 COLUMN",
//                 3: "1 X 3 COLUMN",
//                 4: "1 X 4 COLUMN",
//                 5: "1 X 5 COLUMN",
//                 6: "1 X 6 COLUMN",
//                 7: "1 X 7 COLUMN",
//                 8: "1 X 8 COLUMN"
//             };

//             // <td>${index + 1}</td>
//             data.forEach((room, index) => {
//                 let benchOrderText = benchOrderLabels[room.bench_order] || "Unknown"; // Default to "Unknown" if not mapped
                
//                 let row = `
//                     <tr>
//                         <td>${room.room_no}</td>
//                         <td>${room.room_name}</td>
//                         <td>${benchOrderText}</td>
//                         <td>${room.seat_capacity}</td>
//                         <td class="justify-content-center d-flex">
//                             <button class="ms-2 btn" onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
//                                 <i class="bi bi-pencil-square text-primary"></i>
//                             </button>
//                             <button class="ms-2 btn" onclick="deleteRoom('${room.room_no}')">
//                                 <i class="bi bi-trash-fill text-danger"></i>
//                             </button>
//                         </td>
//                     </tr>
//                 `;
//                 tableBody.innerHTML += row;
//             });
//         })
//         .catch(error => console.error("Error fetching rooms:", error));
// }
// // Populate the edit modal
// function editRoom(roomNo, roomName, benchOrder, capacity) {
//     document.getElementById("editRoomNo").value = roomNo;
//     document.getElementById("editRoomName").value = decodeURIComponent(roomName);
//     document.getElementById("editSize").value = benchOrder;
//     document.getElementById("editCapacity").value = capacity;
    
//     var editModal = new bootstrap.Modal(document.getElementById("editModal"));
//     editModal.show();
// }
// function updateRoom() {
//     let roomNo = document.getElementById("editRoomNo").value.trim();
//     let roomName = document.getElementById("editRoomName").value.trim();
//     let benchOrder = document.getElementById("editSize").value.trim();
//     let seatCapacity = document.getElementById("editCapacity").value.trim();

//     if (!roomNo || !roomName || !benchOrder || !seatCapacity) {
//         alert("Please fill out all fields.");
//         return;
//     }

//     let roomData = {
//         roomNo: roomNo,
//         roomName: roomName,
//         benchOrder: benchOrder,
//         seatCapacity: seatCapacity
//     };

//     fetch("xyz/space/spce_nxe.php", {
//         method: "POST",
//         headers: { "Content-Type": "application/json" },
//         body: JSON.stringify(roomData)
//     })
//     .then(response => response.json())
//     .then(data => {
//         alert(data.message);
//         if (data.success) {
//             location.reload();
//         }
//     })
//     .catch(error => {
//         console.error("Error:", error);
//         alert("An error occurred while updating the room.");
//     });
// }

// function deleteRoom(roomNo) {
//     if (confirm(`Are you sure you want to delete Room No: ${roomNo}? This action cannot be undone.`)) {
//         fetch("xyz/space/spce_nxr.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify({ roomNo })
//         })
//         .then(response => response.json())
//         .then(data => {
//             alert(data.message);
//             if (data.success) {
//                 fetchRooms(); // Refresh the rooms list
//             }
//         })
//         .catch(error => {
//             console.error("❌ Error deleting room:", error);
//             alert("⚠️ An error occurred while deleting the room.");
//         });
//     }
// }


function fetchRooms() {
    fetch("xyz/space/spce_nxo.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("roomTableBody");
            tableBody.innerHTML = "";

            // Sorting the rooms by room_no in ascending order
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

            data.forEach((room, index) => {
                let benchOrderText = benchOrderLabels[room.bench_order] || "Unknown"; 
                
                // let row = `
                //     <tr>
                //         <td>${room.room_no}</td>
                //         <td>${room.room_name}</td>
                //         <td>${benchOrderText}</td>
                //         <td>${room.seat_capacity}</td>
                //         <td class="justify-content-center d-flex">
                //             <button class="ms-2 btn btn-primary d-flex align-items-center gap-1"
                //                 onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
                //                 <i class="bi bi-pencil-square"></i> Edit
                //             </button>
                //             <button class="ms-2 btn btn-danger d-flex align-items-center gap-1"
                //                 onclick="deleteRoom('${room.room_no}')">
                //                 <i class="bi bi-trash-fill"></i> Remove
                //             </button>
                //         </td>

                //     </tr>
                // `;

                // <tr>
                //     <td style="width: 15%;">${room.room_no}</td>
                //     <td style="width: 25%;">${room.room_name}</td>
                //     <td style="width: 20%;">${benchOrderText}</td>
                //     <td style="width: 10%;">${room.seat_capacity}</td>
                //     <td style="width: 100%;" class="text-center">
                //         <div class="d-flex justify-content-center align-items-center gap-2">
                //             <button class="btn btn-primary d-flex align-items-center gap-1"
                //                 onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
                //                 <i class="bi bi-pencil-square"></i> <span>Edit</span>
                //             </button>
                //             <button class="btn btn-danger d-flex align-items-center gap-1"
                //                 onclick="deleteRoom('${room.room_no}')">
                //                 <i class="bi bi-trash-fill"></i> <span>Remove</span>
                //             </button>
                //         </div>
                //     </td>
                // </tr>`
                // const row = `

                // <tr>
                //     <td style="width: 15%;">${room.room_no}</td>
                //     <td style="width: 25%;">${room.room_name}</td>
                //     <td style="width: 20%;">${benchOrderText}</td>
                //     <td style="width: 10%;">${room.seat_capacity}</td>

                //     <td class="text-center" style="width: 100%;">
                //         <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                //             <button class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 py-1"
                //                 onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
                //                 <i class="fas fa-pen"></i> <span>Edit</span>
                //             </button>

                //             <button class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-1"
                //                 onclick="deleteRoom('${room.room_no}')">
                //                 <i class="fas fa-trash"></i> <span>Remove</span>
                //             </button>

                //             <button class="btn btn-outline-warning d-flex align-items-center gap-2 px-3 py-1"
                //                 onclick="toggleRoomStatus('${room.room_no}')">
                //                 <i class="fas fa-toggle-on" id="toggle-icon-${room.room_no}"></i>
                //                 <span id="toggle-label-${room.room_no}">Disable</span>
                //             </button>
                //         </div>
                //     </td>



                // </tr>
                // `;

                
                const toggleIconClass = room.status == 1 ? "fa-toggle-on" : "fa-toggle-off";
                const toggleLabelText = room.status == 1 ? "Disable" : "Enable";

                const row = `
                <tr>
                    <td style="width: 15%;">${room.room_no}</td>
                    <td style="width: 25%;">${room.room_name}</td>
                    <td style="width: 20%;">${benchOrderText}</td>
                    <td style="width: 10%;">${room.seat_capacity}</td>

                    <td class="text-center" style="width: 100%;">
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            <button class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 py-1"
                                onclick="editRoom('${room.room_no}', '${room.room_name}', '${room.bench_order}', '${room.seat_capacity}')">
                                <i class="fas fa-pen"></i> <span>Edit</span>
                            </button>

                            <button class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-1"
                                onclick="deleteRoom('${room.room_no}')">
                                <i class="fas fa-trash"></i> <span>Remove</span>
                            </button>

                            <button class="btn btn-outline-warning d-flex align-items-center gap-2 px-3 py-1"
                                onclick="toggleRoomStatus('${room.room_no}')">
                                <i class="fas ${toggleIconClass}" id="toggle-icon-${room.room_no}"></i>
                                <span id="toggle-label-${room.room_no}">${toggleLabelText}</span>
                            </button>
                        </div>
                    </td>
                </tr>
                `;


                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error("Error fetching rooms:", error);
            Swal.fire("⚠️ Error", "Failed to fetch room data.", "error");
        });
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
        Swal.fire("⚠️ Warning", "Please fill out all fields.", "warning");
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
        Swal.fire({
            title: data.success ? "✅ Success" : "⚠️ Error",
            text: data.message,
            icon: data.success ? "success" : "error"
        }).then(() => {
            if (data.success) {
                location.reload();
            }
        });
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire("⚠️ Error", "An error occurred while updating the room.", "error");
    });
}

function deleteRoom(roomNo) {
    Swal.fire({
        title: "❌ Are you sure?",
        text: `You are about to delete Room No: ${roomNo}. This action cannot be undone!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("xyz/space/spce_nxr.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ roomNo })
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: data.success ? "✅ Deleted!" : "⚠️ Error",
                    text: data.message,
                    icon: data.success ? "success" : "error"
                }).then(() => {
                    if (data.success) {
                        fetchRooms(); // Refresh the rooms list
                    }
                });
            })
            .catch(error => {
                console.error("❌ Error deleting room:", error);
                Swal.fire("⚠️ Error", "An error occurred while deleting the room.", "error");
            });
        }
    });
}


// function toggleRoomStatus(roomNo) {
//     const icon = document.getElementById(`toggle-icon-${roomNo}`);
//     const label = document.getElementById(`toggle-label-${roomNo}`);

//     const isEnabled = icon.classList.contains('fa-toggle-on');

//     if (isEnabled) {
//         icon.classList.remove('fa-toggle-on');
//         icon.classList.add('fa-toggle-off');
//         label.textContent = 'Enable';
//     } else {
//         icon.classList.remove('fa-toggle-off');
//         icon.classList.add('fa-toggle-on');
//         label.textContent = 'Disable';
//     }
// }
function toggleRoomStatus(roomNo) {
    const icon = document.getElementById(`toggle-icon-${roomNo}`);
    const label = document.getElementById(`toggle-label-${roomNo}`);

    const isEnabled = icon.classList.contains('fa-toggle-on');
    // Compute new status to send to backend
    const newStatus = isEnabled ? 0 : 1;

    fetch('xyz/space/toggle_room_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ room_no: roomNo, status: newStatus })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Toggle UI icon and label
            if (newStatus === 1) {
                icon.classList.remove('fa-toggle-off');
                icon.classList.add('fa-toggle-on');
                label.textContent = 'Disable';
            } else {
                icon.classList.remove('fa-toggle-on');
                icon.classList.add('fa-toggle-off');
                label.textContent = 'Enable';
            }
            Swal.fire('Success', `Room ${newStatus === 1 ? 'enabled' : 'disabled'} successfully.`, 'success');
        } else {
            Swal.fire('Error', result.message || 'Failed to update room status.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Network or server error.', 'error');
    });
}
