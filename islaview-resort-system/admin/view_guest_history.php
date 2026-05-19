<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<?php
// 1. Get Guest ID from URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='guests.php';</script>";
    exit();
}

$target_user_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Guest Profile Details
$sql_profile = "SELECT u.*, g.address, g.is_vip,
                (SELECT COUNT(*) FROM bookings WHERE guest_id = g.guest_id) as total_bookings,
                (SELECT SUM(total_amount) FROM bookings WHERE guest_id = g.guest_id AND status != 'cancelled') as total_spent
                FROM users u 
                LEFT JOIN guests g ON u.user_id = g.user_id 
                WHERE u.user_id = '$target_user_id' AND u.role_id = 4";

$res_profile = $conn->query($sql_profile);

if ($res_profile->num_rows == 0) {
    echo "<script>alert('Guest not found!'); window.location='guests.php';</script>";
    exit();
}

$profile = $res_profile->fetch_assoc();
$guest_initials = strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1));
?>

<main class="container main-container">
    <div class="mb-4">
        <a href="guests.php" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Guest Directory
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="bg-primary p-5 text-center">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px; font-size: 2rem; font-weight: 800;">
                            <?php echo $guest_initials; ?>
                        </div>
                        <h4 class="text-white fw-bold mb-1"><?php echo $profile['first_name'] . " " . $profile['last_name']; ?></h4>
                        <?php if($profile['is_vip']): ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-gem me-1"></i> VIP GUEST</span>
                        <?php else: ?>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3">Regular Member</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Contact Information</h6>
                        <div class="mb-3">
                            <label class="small text-muted d-block">Email Address</label>
                            <span class="fw-bold"><?php echo $profile['email']; ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted d-block">Phone Number</label>
                            <span class="fw-bold"><?php echo $profile['phone'] ?: 'N/A'; ?></span>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted d-block">Home Address</label>
                            <span class="fw-bold small"><?php echo $profile['address'] ?: 'No address on file'; ?></span>
                        </div>
                        
                        <hr class="opacity-10">
                        
                        <div class="row text-center mt-4">
                            <div class="col-6 border-end">
                                <h4 class="fw-bold mb-0 text-primary"><?php echo $profile['total_bookings']; ?></h4>
                                <small class="text-muted text-uppercase" style="font-size: 0.6rem;">Stays</small>
                            </div>
                            <div class="col-6">
                                <h4 class="fw-bold mb-0 text-success">₱<?php echo number_format($profile['total_spent'] ?? 0, 0); ?></h4>
                                <small class="text-muted text-uppercase" style="font-size: 0.6rem;">Total Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0">Reservation History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4">Ref Code</th>
                                    <th>Room</th>
                                    <th>Stay Dates</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch all bookings for this specific guest
                                // We need guest_id from the guests table to match bookings
                                $g_id_query = $conn->query("SELECT guest_id FROM guests WHERE user_id = '$target_user_id'");
                                $g_data = $g_id_query->fetch_assoc();
                                $actual_guest_id = $g_data['guest_id'] ?? 0;

                                $sql_history = "SELECT b.*, rt.type_name, r.room_number 
                                                FROM bookings b
                                                JOIN rooms r ON b.room_id = r.room_id
                                                JOIN room_types rt ON r.room_type_id = rt.room_type_id
                                                WHERE b.guest_id = '$actual_guest_id'
                                                ORDER BY b.check_in_date DESC";
                                
                                $res_history = $conn->query($sql_history);

                                if ($res_history && $res_history->num_rows > 0) {
                                    while($row = $res_history->fetch_assoc()) {
                                        $status_class = 'bg-secondary';
                                        if($row['status'] == 'confirmed') $status_class = 'bg-success';
                                        if($row['status'] == 'pending') $status_class = 'bg-warning text-dark';
                                        if($row['status'] == 'checked_in') $status_class = 'bg-info text-white';
                                        if($row['status'] == 'cancelled') $status_class = 'bg-danger';
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">#<?php echo $row['reference_code']; ?></td>
                                            <td>
                                                <div class="small fw-bold">Room <?php echo $row['room_number']; ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?php echo $row['type_name']; ?></div>
                                            </td>
                                            <td class="small">
                                                <?php echo date('M d', strtotime($row['check_in_date'])); ?> - <?php echo date('M d, Y', strtotime($row['check_out_date'])); ?>
                                            </td>
                                            <td class="fw-bold">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php echo $status_class; ?> rounded-pill text-capitalize" style="font-size: 0.7rem;">
                                                    <?php echo str_replace('_', ' ', $row['status']); ?>
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="view_booking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-light border rounded-circle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No booking records found for this guest.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>