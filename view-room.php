<?php include 'includes/header.php'; ?>
<div class="container bg-light vh-100">
    <div class="row g-0 justify-content-center d-flex">
        <!-- <div class="col-lg-12 my-5">
            <h4 class="text-center text-primary text-uppercase fw-semi-bold">Room Details</h4>
        </div> -->
        <div class="col-md-12 col-12 import-section my-5">
            <h2 class="section-title"><i class="fad fa-door-open"></i> Manage Room Details</h2>
            <p class="section-subtext">Easily view, edit, and organize room information and seating capacity</p>
        </div>

        <div class="col-lg-12">
            <!-- <div class="container">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="text-center">
                            <tr>
                                <th>Room No</th>
                                <th>Room Name</th>
                                <th>Bench Order</th>
                                <th>Capacity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody id="roomTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div> -->

            <div class="container">
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-striped table-bordered mb-0">
            <thead class="text-center bg-light sticky-top" style="z-index: 1;">
                <tr>
                    <th style="width: 15%;">Room No</th>
                    <th style="width: 25%;">Room Name</th>
                    <th style="width: 20%;">Bench Order</th>
                    <th style="width: 10%;">Capacity</th>
                    <th style="width: 30%;">Action</th>
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
