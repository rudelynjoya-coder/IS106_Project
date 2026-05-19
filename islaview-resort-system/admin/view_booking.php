<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<?php
// Get booking ID from URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='bookings.php';</script>";
    exit();
}

$booking_id = mysqli_real_escape_string($conn, $_GET['id']);

// SQL Query to get all details including updated payment info
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
            <i class="bi bi-arrow-left me-1"></i> Back to Bookings
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <h2 class="fw-bold text-dark">Booking Details <span class="text-primary">#<?php echo $data['reference_code']; ?></span></h2>
            <div>
                <?php
                $status_btn = 'bg-warning text-dark';
                if($data['status'] == 'confirmed') $status_btn = 'bg-success text-white';
                if($data['status'] == 'checked_in') $status_btn = 'bg-info text-white';
                if($data['status'] == 'checked_out') $status_btn = 'bg-dark text-white';
                if($data['status'] == 'cancelled') $status_btn = 'bg-danger text-white';
                ?>
                <span class="badge <?php echo $status_btn; ?> p-2 px-4 rounded-pill text-capitalize fs-6 shadow-sm">
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
                    <p class="mb-0 opacity-75 small">Para i-confirm ang bayad, i-type ang Reference Code: <strong><?php echo $data['reference_code']; ?></strong></p>
                </div>
                <div class="col-lg-5 mt-3 mt-lg-0">
                    <form action="process_verify_payment.php" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        <input type="hidden" name="actual_ref" value="<?php echo $data['reference_code']; ?>">
                        
                        <input type="text" name="confirm_ref" class="form-control rounded-pill border-0 px-3" 
                               placeholder="Type Booking Ref here..." required autocomplete="off"
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
                    <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary"><i class="bi bi-person-badge me-2"></i>Guest Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">Full Name</label>
                            <span class="fw-bold fs-5 text-dark"><?php echo $data['first_name'] . " " . $data['last_name']; ?></span>
                        </div>
                        <div class="col-md-6 mb-3 text-md-end">
                            <label class="text-muted small d-block">Contact Number</label>
                            <span class="fw-bold text-dark"><?php echo $data['phone'] ?: 'No Phone Provided'; ?></span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block">Email Address</label>
                            <span class="fw-bold text-dark"><?php echo $data['email']; ?></span>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-4 mb-4 border-bottom pb-2 text-primary"><i class="bi bi-calendar-check me-2"></i>Stay Details</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">Check-in</label>
                            <span class="fw-bold text-success fs-5"><?php echo date('M d, Y', strtotime($data['check_in_date'])); ?></span>
                        </div>
                        <div class="col-md-4 mb-3 text-center">
                            <label class="text-muted small d-block">Duration</label>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2"><?php echo $data['nights']; ?> Night(s)</span>
                        </div>
                        <div class="col-md-4 mb-3 text-md-end">
                            <label class="text-muted small d-block">Check-out</label>
                            <span class="fw-bold text-danger fs-5"><?php echo date('M d, Y', strtotime($data['check_out_date'])); ?></span>
                        </div>
                        <div class="col-md-6 mb-3 border-top pt-3">
                            <label class="text-muted small d-block">Room Assigned</label>
                            <span class="fw-bold text-dark fs-6"><?php echo $data['type_name']; ?> (Room <?php echo $data['room_number']; ?>)</span>
                        </div>
                        <div class="col-md-6 mb-3 text-md-end border-top pt-3">
                            <label class="text-muted small d-block">Occupants</label>
                            <span class="fw-bold text-dark"><?php echo $data['num_adults']; ?> Adults, <?php echo $data['num_children']; ?> Kids</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary"><i class="bi bi-receipt me-2"></i>Billing Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Base Amount</span>
                        <span class="fw-bold">₱<?php echo number_format($data['base_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Discount Applied</span>
                        <span class="fw-bold">-₱<?php echo number_format($data['discount_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tax (VAT 12%)</span>
                        <span class="fw-bold">₱<?php echo number_format($data['tax_amount'], 2); ?></span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold m-0">Total Amount</h4>
                        <h3 class="fw-bold text-primary m-0">₱<?php echo number_format($data['total_amount'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                    <h6 class="fw-bold text-uppercase small text-muted m-0">Transaction Info</h6>
                </div>
                <div class="card-body p-4">
                    <?php if($data['payment_status']): ?>
                        <div class="text-center mb-4">
                            <span class="badge <?php echo ($data['payment_status'] == 'verified') ? 'bg-success' : 'bg-warning text-dark'; ?> rounded-pill px-4 py-2 fs-6 mb-3">
                                <?php echo strtoupper($data['payment_status']); ?>
                            </span>
                            <div class="bg-light p-3 rounded-3 text-start mt-2">
                                <div class="small text-muted">Method: <strong class="text-dark"><?php echo strtoupper($data['payment_method']); ?></strong></div>
                                <div class="small text-muted">Ref: <strong class="text-dark"><?php echo $data['transaction_ref'] ?: 'Waiting for verification...'; ?></strong></div>
                                <div class="small text-muted">Amount: <strong class="text-dark">₱<?php echo number_format($data['paid_amount'], 2); ?></strong></div>
                            </div>
                        </div>

                        <?php if($data['screenshot_url']): ?>
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-block text-center">Payment Proof</label>
                            <a href="../assets/uploads/<?php echo $data['screenshot_url']; ?>" target="_blank" class="d-block rounded-3 overflow-hidden border">
                                <img src="../assets/uploads/<?php echo $data['screenshot_url']; ?>" class="img-fluid" alt="Receipt">
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 opacity-50">
                            <i class="bi bi-cash-stack fs-1"></i>
                            <p class="small mt-2">No payment record found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Update Stay Status</h6>
                    <form action="process_booking_status.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        <div class="mb-3">
                            <select name="new_status" class="form-select rounded-3 border-0 bg-light p-3">
                                <option value="pending" <?php if($data['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="confirmed" <?php if($data['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                <option value="checked_in" <?php if($data['status'] == 'checked_in') echo 'selected'; ?>>Checked In</option>
                                <option value="checked_out" <?php if($data['status'] == 'checked_out') echo 'selected'; ?>>Checked Out</option>
                                <option value="cancelled" <?php if($data['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm mb-3">
                            Update Booking Status
                        </button>
                    </form>
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100 rounded-pill py-2 small">
                        <i class="bi bi-printer me-2"></i>Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>