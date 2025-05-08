<?php $current_page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #800000;">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL . 'index.php'; ?>">
            <img src="<?php echo BASE_URL . 'assets/Picture-1.png'; ?>" alt="University Logo" style="height: 45px;">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup"> -->
        <div class="collapse navbar-collapse justify-content-center align-items-center text-center" id="navbarNavAltMarkup" style="height: 100%;">

            <div class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link px-4 py-2 text-white fw-semibold 
        <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="<?php echo BASE_URL . 'index.php'; ?>">
                        Home
                    </a>
                </li>

                <!-- Students Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-4 py-2 text-white fw-semibold 
                        <?php echo ($current_page == 'import-students' || $current_page == 'export-students' || $current_page == 'manage-students') ? 'active' : ''; ?>"
                        href="#" id="studentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Students Manage
                    </a>

                    <ul class="dropdown-menu dropdown-menu-center shadow-lg border-0 mt-2 rounded-4 px-2 py-2"
                        aria-labelledby="studentsDropdown" style="min-width: 250px;">

                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'import-students') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'import-students.php'; ?>">
                                Import Student CSV
                            </a>
                        </li>
                      
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'export-students') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'export-students.php'; ?>">
                                Export Student CSV
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'import-subject') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'import-subject.php'; ?>">
                                Import Subject & Code CSV
                            </a>
                        </li>  
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'export-subject') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'export-subject.php'; ?>">
                                Export Subject & Code CSV
                            </a>
                        </li>                        
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'manage-students') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'manage-students.php'; ?>">
                                View Student
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Room Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-4 py-2 text-white fw-semibold 
                        <?php echo ($current_page == 'add-room' || $current_page == 'view-room') ? 'active' : ''; ?>"
                        href="#" id="roomDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Room Manage
                    </a>

                    <ul class="dropdown-menu dropdown-menu-center shadow-lg border-0 mt-2 rounded-4 px-2 py-2"
                        aria-labelledby="roomDropdown" style="min-width: 220px;">

                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'add-room') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'add-room.php'; ?>">
                                Add Room
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'view-room') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'view-room.php'; ?>">
                                View Room
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Examination Plan Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-4 py-2 text-white fw-semibold 
                        <?php echo ($current_page == 'advance-seat-plan' || $current_page == 'attendance-sheet' || $current_page == 'top-sheet' || $current_page == 'manage-department') ? 'active' : ''; ?>"
                        href="#" id="examinationPlanDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Examination Planning
                    </a>

                    <ul class="dropdown-menu dropdown-menu-center shadow-lg border-0 mt-2 rounded-4 px-2 py-2"
                        aria-labelledby="examinationPlanDropdown" style="min-width: 250px;">
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'manage-department') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'manage-department.php'; ?>">
                                Manage Department
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'advance-seat-plan') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'advance-seat-plan.php'; ?>">
                                Hall Ticket Preparation
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'advance-seat-plan') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'seat-summary.php'; ?>">
                                 Seat Plan Summary
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'attendance-sheet') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'attendance-sheet.php'; ?>">
                                Attendance Sheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'top-sheet') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'top-sheet.php'; ?>">
                                Advance Top Sheet
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Dropdown for Help and About -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-4 py-2 text-white fw-semibold 
                        <?php echo ($current_page == 'help' || $current_page == 'about') ? 'active' : ''; ?>" href="#"
                        id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle me-2 text-white"></i>
                        Account
                    </a>

                    <ul class="dropdown-menu dropdown-menu-center shadow-lg border-0 mt-2 rounded-4 px-2 py-2"
                        aria-labelledby="moreDropdown" style="min-width: 200px;">
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'profile') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'profile.php'; ?>">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'change-password') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'change-password.php'; ?>">
                                Change Password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'logout') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'logout.php'; ?>">
                                Logout
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'help') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'help.php'; ?>">
                                Help
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded <?php echo ($current_page == 'about') ? 'active bg-light fw-bold' : ''; ?>"
                                href="<?php echo BASE_URL . 'about.php'; ?>">
                                About
                            </a>
                        </li>
                    </ul>
                </li>

            </div>
        </div>
    </div>
</nav>