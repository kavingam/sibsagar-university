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

                <!-- Students Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($current_page == 'import-students' || $current_page == 'export-students' || $current_page == 'manage-students') ? 'active' : ''; ?>"
                       href="#" id="studentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Students
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="studentsDropdown" style="">
                        <li><a class="dropdown-item <?php echo ($current_page == 'import-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'import-students.php'; ?>">Import CSV</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'export-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'export-students.php'; ?>">Export CSV</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'manage-students') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'manage-students.php'; ?>">Manage Student</a></li>
                    </ul>
                </li>

                <!-- Room Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($current_page == 'add-room' || $current_page == 'view-room') ? 'active' : ''; ?>"
                       href="#" id="roomDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Room
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="roomDropdown">
                        <li><a class="dropdown-item <?php echo ($current_page == 'add-room') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'add-room.php'; ?>">Add Room</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'view-room') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'view-room.php'; ?>">View Room</a></li>
                    </ul>
                </li>



                <!-- Examination Plan Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($current_page == 'advance-seat-plan' || $current_page == 'attendance-sheet' || $current_page == 'top-sheet') ? 'active' : ''; ?>"
                       href="#" id="examinationPlanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Examination Plan
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="examinationPlanDropdown">
                        <li><a class="dropdown-item <?php echo ($current_page == 'manage-department') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'manage-department.php'; ?>">Manage Department</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'advance-seat-plan') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'advance-seat-plan.php'; ?>">Advance Seat Plan</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'attendance-sheet') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'attendance-sheet.php'; ?>">Attendance Sheet</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'top-sheet') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'top-sheet.php'; ?>">Advance Top Sheet</a></li>
                    </ul>
                </li>
                <!-- Dropdown for Help and About -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($current_page == 'help' || $current_page == 'about') ? 'active' : ''; ?>"
                       href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        More
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item <?php echo ($current_page == 'help') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'help.php'; ?>">Help</a></li>
                        <li><a class="dropdown-item <?php echo ($current_page == 'about') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'about.php'; ?>">About</a></li>
                    </ul>
                </li>
                
            </div>
        </div>
    </div>
</nav>