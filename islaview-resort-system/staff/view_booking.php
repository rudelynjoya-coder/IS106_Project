<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<?php
// 1. Kunin ang Booking ID mula sa URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='bookings.php';</script>";
    exit();
}

$booking_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Comprehensive SQL Query
$sql = "SELECT b.*, g.*, r.room_number, rt.type_name, rt.base_price as room_rate,
        p.payment_id, p.payment_method, p.transaction_ref, p.amount as paid_amount, p.status as payment_status, p.screenshot_url
        FROM bookings b
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.booking_id = '$booking_id'";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<script>alert('Booking not found!'); window.location='bookings.php';</script>";
    exit();
}
$data = $result->fetch_assoc();
?>

<main class="container main-container">
    <div class="mb-4">
        <a href="bookings.php" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to All Bookings
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <h2 class="fw-bold m-0 text-dark">Booking Details <span class="text-primary">#<?php echo $data['reference_code']; ?></span></h2>
            <div>
                <?php
                $status_bg = 'bg-secondary';
                if($data['status'] == 'confirmed') $status_bg = 'bg-success';
                if($data['status'] == 'checked_in') $status_bg = 'bg-info text-white';
                if($data['status'] == 'checked_out') $status_bg = 'bg-dark';
                if($data['status'] == 'cancelled') $status_bg = 'bg-danger';
                ?>
                <span class="badge <?php echo $status_bg; ?> p-2 px-4 rounded-pill fs-6 text-capitalize shadow-sm">
                    <?php echo str_replace('_', ' ', $data['status']); ?>
                </span>
            </div>
        </div>
    </div>

    <?php if ($data['payment_status'] !== 'verified' && $data['status'] !== 'cancelled'): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock me-2"></i>Verify Payment Reference</h5>
                    <p class="mb-0 opacity-75 small">Para ma-confirm ang bayad at payagan ang check-in, i-type ang Ref: <strong><?php echo $data['reference_code']; ?></strong></p>
                </div>
                <div class="col-lg-5 mt-3 mt-lg-0">
                    <form action="process_verify_payment.php" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        <input type="hidden" name="actual_ref" value="<?php echo $data['reference_code']; ?>">
                        
                        <input type="text" name="confirm_ref" class="form-control rounded-pill border-0 px-3" 
                               placeholder="Type Code here..." required autocomplete="off"
                               style="text-transform: uppercase; font-weight: bold;">
                        
                        <button type="submit" class="btn btn-light rounded-pill px-4 fw-bold shadow text-primary">
                            VERIFY
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold mb-3 text-primary">Guest Profile</h6>
                            <h4 class="fw-bold mb-1"><?php echo $data['first_name'] . " " . $data['last_name']; ?></h4>
                            <p class="mb-1 text-dark small"><i class="bi bi-telephone me-2"></i><?php echo $data['phone'] ?: 'N/A'; ?></p>
                            <p class="mb-0 text-dark small"><i class="bi bi-envelope me-2"></i><?php echo $data['email']; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end border-start">
                            <h6 class="text-muted small text-uppercase fw-bold mb-3">Accommodation</h6>
                            <h4 class="fw-bold text-dark mb-1">Room <?php echo $data['room_number']; ?></h4>
                            <p class="mb-0 text-muted"><?php echo $data['type_name']; ?></p>
                        </div>
                    </div>

                    <hr class="opacity-10">

                    <div class="row mt-4">
                        <div class="col-md-4 text-center">
                            <small class="text-muted d-block">Arrival Date</small>
                            <span class="fw-bold fs-5 text-success"><?php echo date('M d, Y', strtotime($data['check_in_date'])); ?></span>
                        </div>
                        <div class="col-md-4 text-center border-start border-end">
                            <small class="text-muted d-block">Stay Duration</small>
                            <span class="fw-bold fs-5"><?php echo $data['nights']; ?> Night(s)</span>
                        </div>
                        <div class="col-md-4 text-center">
                            <small class="text-muted d-block">Departure Date</small>
                            <span class="fw-bold fs-5 text-danger"><?php echo date('M d, Y', strtotime($data['check_out_date'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-4">Billing Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Room Charges</span>
                        <span class="fw-bold">₱<?php echo number_format($data['base_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Applied Discount</span>
                        <span class="fw-bold">-₱<?php echo number_format($data['discount_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span>VAT (12%)</span>
                        <span class="fw-bold">₱<?php echo number_format($data['tax_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold m-0">Total Amount</h4>
                        <h3 class="fw-bold text-primary m-0">₱<?php echo number_format($data['total_amount'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3 text-center">Transaction Status</h6>
                    <?php if($data['payment_status'] == 'verified'): ?>
                        <div class="text-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-patch-check-fill fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-success mb-0">FULLY PAID</h5>
                            <small class="text-muted d-block mt-2">Ref: <?php echo $data['transaction_ref']; ?></small>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-clock-history fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-warning mb-0">UNVERIFIED</h5>
                            <p class="small text-muted mt-2">Verify payment first to enable check-in.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-4">Operational Actions</h6>
                    
                    <form action="process_staff_actions.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        
                        <?php if($data['status'] == 'pending' || $data['status'] == 'confirmed'): ?>
                            <button type="submit" name="action" value="check_in" 
                                    class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3 shadow-sm <?php echo ($data['payment_status'] !== 'verified') ? 'disabled opacity-50' : ''; ?>">
                                <i class="bi bi-box-arrow-in-right me-2"></i>CONFIRM CHECK-IN
                            </button>
                        <?php elseif($data['status'] == 'checked_in'): ?>
                            <button type="submit" name="action" value="check_out" class="btn btn-danger w-100 py-3 rounded-pill fw-bold mb-3 shadow-sm">
                                <i class="bi bi-box-arrow-left me-2"></i>CONFIRM CHECK-OUT
                            </button>
                        <?php endif; ?>

                        <?php if($data['status'] !== 'checked_out' && $data['status'] !== 'cancelled'): ?>
                             <button type="submit" name="action" value="cancel" class="btn btn-link text-danger w-100 fw-bold text-decoration-none" 
                                     onclick="return confirm('Sigurado ka bang i-cacancel ito?')">
                                Cancel Reservation
                            </button>
                        <?php else: ?>
                             <button class="btn btn-light w-100 py-3 rounded-pill fw-bold disabled border">STAY COMPLETED</button>
                        <?php endif; ?>
                    </form>
                    <hr class="my-4">
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100 rounded-pill py-2 small">
                        <i class="bi bi-printer me-2"></i>Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>