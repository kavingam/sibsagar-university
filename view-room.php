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
<div class="container-fluid bg-light min-vh-100 py-4">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title"><i class="fas fa-door-open"></i> Manage Room Details</h2>
            <p class="text-muted">Easily view, edit, and organize room information and seating capacity</p>
        </div>

        <div class="col-lg-10">
            <div class="table-responsive rounded-3 shadow-sm">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="text-center text-white bg-dark sticky-top" style="z-index: 1;">
                        <tr>
                            <th style="width: 15%;">Room No</th>
                            <th style="width: 25%;">Room Name</th>
                            <th style="width: 20%;">Bench Order</th>
                            <th style="width: 10%;">Capacity</th>
                            <th style="width: 30%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="roomTableBody" class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
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
                    <label for="editSize">Bench Order:</label>
                    <select id="editSize" class="form-select">
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
                <div class="mb-3">
                    <label class="form-label">Bench Capacity:</label>
                    <input type="number" id="editCapacity" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times-square"></i> Close</button>
                <button type="button" class="btn btn-primary" onclick="updateRoom()"><i class="fas fa-chevron-square-up"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    fetchRooms();
});
</script>

<?php include 'includes/footer.php'; ?>
