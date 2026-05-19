<?php
// Kunin ang pangalan ng kasalukuyang file
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-islands me-2"></i>ISLAVIEW ADMIN
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>" href="bookings.php">Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>" href="rooms.php">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'guests.php') ? 'active' : ''; ?>" href="guests.php">Guests</a>
                </li>

                <!-- STAFF MENU: Lalabas lang kung ang Role ID ay 1 (Superadmin) o 2 (Admin) -->
                <?php if ($_SESSION['role_id'] <= 2): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'staff.php') ? 'active' : ''; ?>" href="staff.php">Staff</a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" href="reports.php">Reports</a>
                </li>
            </ul>
            
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?php echo $_SESSION['fname']; ?>
                    <span class="badge bg-info text-dark ms-1" style="font-size: 0.7rem;">
                        <?php 
                            if($_SESSION['role_id'] == 1) echo "Superadmin";
                            elseif($_SESSION['role_id'] == 2) echo "Admin";
                            else echo "Staff";
                        ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                    <li><a class="dropdown-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="profile.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>