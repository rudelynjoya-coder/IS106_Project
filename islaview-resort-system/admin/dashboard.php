<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Management Overview</h2>
            <p class="text-muted">Welcome back! Here is what's happening today.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="mb-0 fw-bold"><?php echo date('l'); ?></h5>
            <span class="text-primary"><?php echo date('F d, Y'); ?></span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-primary text-white h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Total Bookings</h6>
                        <?php 
                            $b_count = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc();
                            echo "<h2 class='fw-bold mb-0'>".$b_count['total']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box"><i class="bi bi-calendar-check"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-success text-white h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Available Rooms</h6>
                        <?php 
                            $r_count = $conn->query("SELECT COUNT(*) as total FROM room_current_status WHERE status = 'available'")->fetch_assoc();
                            echo "<h2 class='fw-bold mb-0'>".$r_count['total']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box"><i class="bi bi-door-open"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-warning text-dark h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Pending Payments</h6>
                        <?php 
                            $p_count = $conn->query("SELECT COUNT(*) as total FROM payments WHERE status = 'pending'")->fetch_assoc();
                            echo "<h2 class='fw-bold mb-0'>".$p_count['total']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box border border-dark border-opacity-10"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-white text-dark h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Active Guests</h6>
                        <?php 
                            $g_count = $conn->query("SELECT COUNT(*) as total FROM guests")->fetch_assoc();
                            echo "<h2 class='fw-bold mb-0'>".$g_count['total']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box bg-light text-primary"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="mt-5">
        <h4 class="fw-bold mb-3">Recent Bookings</h4>
        <div class="card table-card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Reference</th>
                                <th>Guest Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_recent = "SELECT b.*, g.first_name, g.last_name 
                                           FROM bookings b 
                                           JOIN guests g ON b.guest_id = g.guest_id 
                                           ORDER BY b.created_at DESC LIMIT 5";
                            $res_recent = $conn->query($sql_recent);

                            if($res_recent->num_rows > 0) {
                                while($row = $res_recent->fetch_assoc()) {
                                    $status_class = 'bg-secondary';
                                    if($row['status'] == 'confirmed') $status_class = 'bg-success';
                                    if($row['status'] == 'pending') $status_class = 'bg-warning text-dark';
                                    
                                    echo "<tr>
                                        <td class='ps-4 fw-bold'>#".$row['reference_code']."</td>
                                        <td>".$row['first_name']." ".$row['last_name']."</td>
                                        <td>".date('M d, Y', strtotime($row['check_in_date']))."</td>
                                        <td>".date('M d, Y', strtotime($row['check_out_date']))."</td>
                                        <td><span class='badge $status_class px-3 rounded-pill text-capitalize'>".$row['status']."</span></td>
                                        <td class='pe-4 text-end'>
                                            <a href='view_booking.php?id=".$row['booking_id']."' class='btn btn-sm btn-outline-primary rounded-pill'>View</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No recent transactions recorded.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>