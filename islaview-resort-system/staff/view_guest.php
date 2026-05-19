<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<?php
// Get Guest ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='guests.php';</script>";
    exit();
}

$guest_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch Guest Profile
$guest_res = $conn->query("SELECT * FROM guests WHERE guest_id = '$guest_id'");
if ($guest_res->num_rows == 0) {
    echo "<script>alert('Guest not found!'); window.location='guests.php';</script>";
    exit();
}
$guest = $guest_res->fetch_assoc();

// Fetch Guest Statistics
$stats = $conn->query("SELECT 
    COUNT(booking_id) as total_bookings,
    SUM(CASE WHEN status = 'checked_out' THEN 1 ELSE 0 END) as completed_stays,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancellations,
    SUM(total_amount) as total_spent
    FROM bookings WHERE guest_id = '$guest_id'")->fetch_assoc();
?>

<main class="container main-container">
    <div class="mb-4">
        <a href="guests.php" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Guest Directory
        </a>
    </div>

    <div class="row g-4">
        <!-- Guest Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
                <div class="avatar-large mx-auto bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem; border: 2px solid var(--primary-blue);">
                    <?php echo substr($guest['first_name'], 0, 1) . substr($guest['last_name'], 0, 1); ?>
                </div>
                <h4 class="fw-bold mb-1"><?php echo $guest['first_name'] . " " . $guest['last_name']; ?></h4>
                <p class="text-muted small mb-4">Guest since <?php echo date('M Y', strtotime($guest['created_at'])); ?></p>
                
                <div class="text-start border-top pt-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Contact Information</h6>
                    <p class="small mb-2"><i class="bi bi-envelope me-2 text-primary"></i><?php echo $guest['email']; ?></p>
                    <p class="small mb-0"><i class="bi bi-telephone me-2 text-primary"></i><?php echo $guest['phone']; ?></p>
                </div>
            </div>

            <!-- Guest Value Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
                <h6 class="opacity-75 small text-uppercase fw-bold mb-3">Total Revenue From Guest</h6>
                <h2 class="fw-bold mb-0">₱<?php echo number_format($stats['total_spent'] ?? 0, 2); ?></h2>
                <hr class="opacity-25">
                <div class="row text-center">
                    <div class="col-6 border-end border-white border-opacity-25">
                        <small class="d-block opacity-75">Stays</small>
                        <span class="fw-bold"><?php echo $stats['completed_stays']; ?></span>
                    </div>
                    <div class="col-6">
                        <small class="d-block opacity-75">Cancelled</small>
                        <span class="fw-bold"><?php echo $stats['cancellations']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking History Section -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold m-0">Booking History</h4>
                <span class="badge bg-white text-dark border rounded-pill px-3 py-2"><?php echo $stats['total_bookings']; ?> Total Records</span>
            </div>

            <div class="card table-card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Ref Code</th>
                                <th>Room</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $history_sql = "SELECT b.*, r.room_number 
                                            FROM bookings b 
                                            JOIN rooms r ON b.room_id = r.room_id 
                                            WHERE b.guest_id = '$guest_id' 
                                            ORDER BY b.check_in_date DESC";
                            $history_res = $conn->query($history_sql);

                            if($history_res->num_rows > 0):
                                while($row = $history_res->fetch_assoc()):
                                    $st_class = 'bg-secondary';
                                    if($row['status'] == 'confirmed') $st_class = 'bg-success';
                                    if($row['status'] == 'checked_in') $st_class = 'bg-info text-white';
                                    if($row['status'] == 'checked_out') $st_class = 'bg-dark';
                                    if($row['status'] == 'cancelled') $st_class = 'bg-danger';
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo $row['reference_code']; ?></td>
                                <td>Room <?php echo $row['room_number']; ?></td>
                                <td>
                                    <small class="d-block fw-medium"><?php echo date('M d', strtotime($row['check_in_date'])); ?> - <?php echo date('M d, Y', strtotime($row['check_out_date'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $st_class; ?> rounded-pill text-capitalize" style="font-size: 0.75rem;">
                                        <?php echo str_replace('_', ' ', $row['status']); ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="view_booking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-light border rounded-pill">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'>This guest has no booking records yet.</td></tr>";
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>