<?php $current_page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>
<nav class="navbar navbar-expand-lg" style="background-color: #800000;">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL . 'index.php'; ?>">
            <img src="<?php echo BASE_URL . 'assets/Picture-1.png'; ?>" alt="University Logo" style="height: 45px;">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'index.php'; ?>">Home</a>
                <a class="nav-link <?php echo ($current_page == 'import-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'import-students.php'; ?>">Import CSV</a>
                <a class="nav-link <?php echo ($current_page == 'export-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'export-students.php'; ?>">Export CSV</a>
                <a class="nav-link <?php echo ($current_page == 'manage-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'manage-students.php'; ?>">Manage Student</a>
                <a class="nav-link <?php echo ($current_page == 'add-room') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'add-room.php'; ?>">Add Room</a>
                <a class="nav-link <?php echo ($current_page == 'view-room') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'view-room.php'; ?>">View Room</a>
                <a class="nav-link <?php echo ($current_page == 'manage-department') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'manage-department.php'; ?>">Manage Department</a>
                <a class="nav-link <?php echo ($current_page == 'advance-seat-plan') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'advance-seat-plan.php'; ?>">Advance Seat Plan</a>
                <a class="nav-link <?php echo ($current_page == 'attendance-sheet') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'attendance-sheet.php'; ?>">Attendance Sheet</a>
                <a class="nav-link <?php echo ($current_page == 'top-sheet') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'top-sheet.php'; ?>">Advance Top Sheet</a>
            </div>
        </div>
    </div>
</nav>