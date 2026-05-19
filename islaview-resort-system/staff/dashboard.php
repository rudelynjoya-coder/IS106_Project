<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-dark m-0">Staff Operations</h2>
            <p class="text-muted">Tracking arrivals and departures for today.</p>
        </div>
        <div class="col-md-5 text-md-end">
            <div id="digitalClock" class="fw-bold fs-4" style="color: var(--primary-blue);"></div>
            <span class="text-muted small"><?php echo date('F d, Y'); ?></span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-primary h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75 small text-uppercase fw-bold">Today's Arrivals</h6>
                        <?php 
                        $arrivals = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE check_in_date = '$today' AND status='confirmed'")->fetch_assoc();
                        echo "<h2 class='fw-bold mb-0'>".$arrivals['t']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box"><i class="bi bi-box-arrow-in-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-danger h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75 small text-uppercase fw-bold">Departures</h6>
                        <?php 
                        $departures = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE check_out_date = '$today' AND status='checked_in'")->fetch_assoc();
                        echo "<h2 class='fw-bold mb-0'>".$departures['t']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box"><i class="bi bi-box-arrow-left"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-warning h-100 text-dark">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75 small text-uppercase fw-bold">Need Cleaning</h6>
                        <?php 
                        $dirty = $conn->query("SELECT COUNT(*) as t FROM room_current_status WHERE status='cleaning'")->fetch_assoc();
                        echo "<h2 class='fw-bold mb-0'>".$dirty['t']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box" style="background: rgba(0,0,0,0.1);"><i class="bi bi-stars"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats shadow-sm p-4 bg-success h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75 small text-uppercase fw-bold">Available Now</h6>
                        <?php 
                        $available = $conn->query("SELECT COUNT(*) as t FROM room_current_status WHERE status='available'")->fetch_assoc();
                        echo "<h2 class='fw-bold mb-0'>".$available['t']."</h2>";
                        ?>
                    </div>
                    <div class="icon-box"><i class="bi bi-check2-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expected Arrivals Table -->
    <div class="row">
        <div class="col-12">
            <div class="card table-card shadow-sm">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold m-0 text-dark">Expected Arrivals Today</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Reference</th>
                                <th>Guest Name</th>
                                <th>Room Number</th>
                                <th>Payment Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT b.*, g.first_name, g.last_name, r.room_number 
                                    FROM bookings b 
                                    JOIN guests g ON b.guest_id = g.guest_id 
                                    JOIN rooms r ON b.room_id = r.room_id
                                    WHERE b.check_in_date = '$today' AND b.status = 'confirmed'";
                            $res = $conn->query($sql);
                            if($res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    echo "<tr>
                                        <td class='ps-4 fw-bold text-primary'>#{$row['reference_code']}</td>
                                        <td class='fw-bold text-dark'>{$row['first_name']} {$row['last_name']}</td>
                                        <td><span class='badge bg-light text-dark border px-3'>Room {$row['room_number']}</span></td>
                                        <td><span class='text-success small fw-bold'><i class='bi bi-patch-check-fill me-1'></i> Verified</span></td>
                                        <td class='pe-4 text-end'>
                                            <a href='view_booking.php?id={$row['booking_id']}' class='btn btn-checkin btn-sm'>Check-in Guest</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No arrivals expected today.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>