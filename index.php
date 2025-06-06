<?php
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
// echo $userEmail;
?>
<?php
include 'includes/header.php';
include 'xyz/bashmodel.php';

$stdObj = new Student();
$deptObj = new Department();
$roomObj = new Room();
$userObj = new UserInfo();
?>
<!-- <div class="container vh-100 d-flex flex-column justify-content-center align-items-center bg-light text-center">
    <img src="assets/image.png" alt="Seat Plan Logo" class="img-fluid mb-4" style="max-width: 200px;">
    <h1 class="">Advance Automated Seat Plan Generator</h1>
    <p class="mt-3">       
        <a href="https://sibsagaruniversity.ac.in/" class="text-decoration-none text-primary fw-semibold" target="_blank" rel="noopener noreferrer">
            Visit Sibsagar University
        </a>
    </p>
</div> -->



<div class="mydash-container container">
    <!-- Main content -->
    <div class="mydash-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mydash-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Dashboard</a>
                <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button> -->
            </div>
        </nav>

<!-- Stats Cards -->
<div class="row mydash-stats-row">
    <!-- Total Students -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-user-graduate"></i> Total Students
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($stdObj->getAllStudents()); ?></h5>
                <p class="card-text">Total number of enrolled students.</p>
                <a href="students.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> View Students
                </a>
            </div>
        </div>
    </div>

    <!-- Total Department -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-building"></i> Total Department
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($deptObj->getAllDepartments()); ?></h5>
                <p class="card-text">Number of active academic departments.</p>
                <a href="department.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> View Departments
                </a>
            </div>
        </div>
    </div>

    <!-- Total Rooms -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-door-open"></i> Total Rooms
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($roomObj->getAllRooms()); ?></h5>
                <p class="card-text">Allotable classrooms for seating plans.</p>
                <a href="room.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> View Rooms
                </a>
            </div>
        </div>
    </div>

    <!-- Total Exams -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-file-alt"></i> Total Exams
            </div>
            <div class="card-body">
                <h5 class="card-title">12</h5>
                <p class="card-text">Scheduled or past exam sessions.</p>
                <a href="seat-allotment.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> View Exams
                </a>
            </div>
        </div>
    </div>
    <!-- Total Topsheet  -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-file-alt"></i> Total Top Sheet
            </div>
            <div class="card-body">
                <h5 class="card-title">12</h5>
                <p class="card-text">Exams Top Sheet List.</p>
                <a href="top-sheet.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> View Exams
                </a>
            </div>
        </div>
    </div>

    <!-- Activate User -->
    <div class="col-md-4">
        <div class="card mydash-card">
            <div class="card-header mydash-card-header">
                <i class="fas fa-user-check"></i> Activate Users
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($userObj->getUserCount()); ?></h5>
                <p class="card-text">Total active admin or faculty users.</p>
                <a href="users.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-circle-right"></i> Manage Users
                </a>
            </div>
        </div>
    </div>
</div>


        <!-- Recent Activity Table -->
        <!-- <div class="card mt-4 mydash-activity-card">
            <div class="card-header">
                Recent Seat Plan Summary
            </div>
            <div class="card-body">
                <table class="table table-bordered mydash-table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>User A logged in</td>
                            <td>2 hours ago</td>
                        </tr>
                        <tr>
                            <td>New sale: Product X</td>
                            <td>4 hours ago</td>
                        </tr>
                        <tr>
                            <td>Profile update</td>
                            <td>6 hours ago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> -->

    </div>
</div>



<!-- Notification Card Container -->
<div id="popupContainer"
     class="position-fixed top-50 start-50 translate-middle shadow"
     style="z-index: 1055; width: 100%; max-width: 700px; display: none;">

    <div class="card border-successx g-0">
        <!-- Header with Countdown -->
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle me-2"></i>Wlecome to Sibsagar University</span>
            <div id="countdownCircle"
                 class="rounded-circle bg-white text-dark fw-bold d-flex align-items-center justify-content-center"
                 style="width: 35px; height: 35px; border: 2px solid #fff;">
                <span id="countdownTimer">8</span>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <div class="container vh-100x d-flex flex-column justify-content-center align-items-center bg-light text-center">
                <img src="assets/image.png" alt="Seat Plan Logo" class="img-fluid mb-4" style="max-width: 200px;">
                <h1 class="">Advance Automated Seat Plan Generator</h1>
                <p class="mt-3">       
                    <a href="https://sibsagaruniversity.ac.in/" class="text-decoration-none text-primary fw-semibold" target="_blank" rel="noopener noreferrer">
                        Visit Sibsagar University
                    </a>
                </p>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-end bg-light">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="closePopup()">Close</button>
        </div>
    </div>
</div>



<!-- <a href="#" class="btn-lightweight btn-primary-light rounded-0"><i class="fas fa-info-circle"></i> Primary</a>
<a href="#" class="btn-lightweight btn-secondary-light"><i class="fas fa-layer-group"></i> Secondary</a>
<a href="#" class="btn-lightweight btn-success-light"><i class="fas fa-check-circle"></i> Success</a>
<a href="#" class="btn-lightweight btn-info-light"><i class="fas fa-info"></i> Info</a>
<a href="#" class="btn-lightweight btn-warning-light"><i class="fas fa-exclamation-triangle"></i> Warning</a>
<a href="#" class="btn-lightweight btn-danger-light"><i class="fas fa-times-circle"></i> Danger</a>
<a href="#" class="btn-lightweight btn-light-light"><i class="fas fa-sun"></i> Light</a>
<a href="#" class="btn-lightweight btn-dark-light"><i class="fas fa-moon"></i> Dark</a> -->

<!-- Countdown Script -->
<script>
// function closePopup() {
//     document.getElementById('popupContainer').style.display = 'none';
// }

// document.addEventListener('DOMContentLoaded', function () {
//     const popup = document.getElementById('popupContainer');
//     const timer = document.getElementById('countdownTimer');

//     let seconds = 8;
//     popup.style.display = 'block';

//     const countdown = setInterval(() => {
//         seconds--;
//         timer.textContent = seconds;

//         if (seconds <= 0) {
//             clearInterval(countdown);
//             closePopup();
//         }
//     }, 1000);
// });
</script>



<?php include 'includes/footer.php'; ?>