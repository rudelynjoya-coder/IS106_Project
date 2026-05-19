<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Guest Directory</h2>
            <p class="text-muted">View and manage guest contact information and history.</p>
        </div>
        <!-- Search bar for finding specific guests -->
        <div class="col-md-4">
            <form action="" method="GET">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0" placeholder="Name, Email, or Phone..." value="<?php echo $_GET['search'] ?? ''; ?>">
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats for Guests -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Registered</small>
                        <?php $total = $conn->query("SELECT COUNT(*) as t FROM guests")->fetch_assoc(); ?>
                        <h4 class="fw-bold mb-0"><?php echo $total['t']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-info border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Repeat Guests</small>
                        <?php 
                        // Logic: Guests with more than 1 booking
                        $repeat = $conn->query("SELECT COUNT(*) as t FROM (SELECT guest_id FROM bookings GROUP BY guest_id HAVING COUNT(booking_id) > 1) as sub")->fetch_assoc(); 
                        ?>
                        <h4 class="fw-bold mb-0"><?php echo $repeat['t']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">New This Month</small>
                        <?php 
                        $month = date('m');
                        $new = $conn->query("SELECT COUNT(*) as t FROM guests WHERE MONTH(created_at) = '$month'")->fetch_assoc(); 
                        ?>
                        <h4 class="fw-bold mb-0"><?php echo $new['t']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Table -->
    <div class="card table-card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Guest Profile</th>
                        <th>Contact Information</th>
                        <th>Last Stay</th>
                        <th>Total Bookings</th>
                        <th class="pe-4 text-end">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $search_query = " WHERE first_name LIKE '%$s%' OR last_name LIKE '%$s%' OR email LIKE '%$s%' OR phone LIKE '%$s%'";
                    }

                    $sql = "SELECT g.*, 
                            (SELECT COUNT(booking_id) FROM bookings WHERE guest_id = g.guest_id) as booking_count,
                            (SELECT MAX(check_in_date) FROM bookings WHERE guest_id = g.guest_id) as last_stay
                            FROM guests g 
                            $search_query
                            ORDER BY g.last_name ASC";
                    
                    $res = $conn->query($sql);

                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-light text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; border: 1px solid #dee2e6;">
                                    <?php echo substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1); ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $row['first_name'] . " " . $row['last_name']; ?></div>
                                    <small class="text-muted">Member since <?php echo date('M Y', strtotime($row['created_at'])); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-envelope me-2 text-muted"></i><?php echo $row['email']; ?></div>
                            <div class="small"><i class="bi bi-telephone me-2 text-muted"></i><?php echo $row['phone']; ?></div>
                        </td>
                        <td>
                            <?php if($row['last_stay']): ?>
                                <span class="small text-dark"><?php echo date('M d, Y', strtotime($row['last_stay'])); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">No stays yet</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <?php echo $row['booking_count']; ?> Bookings
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="view_guest.php?id=<?php echo $row['guest_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> History
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No guests found in the directory.</td></tr>";
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>