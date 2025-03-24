<?php include 'includes/header.php'; ?>
<div class="container my-4">
    <div class="row g-0 justify-content-center d-flex">
        <div class="col-lg-12 my-3">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Room Details</h4>
        </div>
        <div class="col-lg-12">
            <div class="container">
                <div class="overflow-auto table-responsive">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="text-center">
                        <tr>
                            <!-- <th>SNO</th> -->
                            <th>Room No</th>
                            <th>Room Name</th>
                            <th>Bench Order</th>
                            <th>Capacity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="roomTableBody"></tbody>
                </table>
                </div>
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
                    <label class="form-label">Capacity:</label>
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
<script>
    
</script>
<?php include 'includes/footer.php'; ?>
