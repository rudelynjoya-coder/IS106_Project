<?php 
include 'components/header.php'; 
include 'components/navbar.php'; 

// 1. Get Guest ID from URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='guests.php';</script>";
    exit();
}

$guest_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Guest Profile Details
$guest_query = "SELECT * FROM guests WHERE guest_id = '$guest_id'";
$guest_res = $conn->query($guest_query);

if ($guest_res->num_rows == 0) {
    echo "<script>alert('Guest record not found.'); window.location='guests.php';</script>";
    exit();
}
$guest = $guest_res->fetch_assoc();

// 3. Fetch Guest Statistics (Business Intelligence)
$stats_query = "SELECT 
    COUNT(booking_id) as total_bookings,
    SUM(CASE WHEN status = 'checked_out' THEN 1 ELSE 0 END) as successful_stays,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as total_cancellations,
    SUM(total_amount) as lifetime_value
    FROM bookings WHERE guest_id = '$guest_id'";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<main class="container main-container">
    <!-- Breadcrumb & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="guests.php" class="text-decoration-none">Guest List</a></li>
                    <li class="breadcrumb-item active">Guest Profile</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark m-0">Guest Details</h2>
        </div>
        <button class="btn btn-outline-danger rounded-pill px-4" onclick="confirmDelete(<?php echo $guest_id; ?>)">
            <i class="bi bi-trash me-2"></i>Remove Guest
        </button>
    </div>

    <div class="row g-4">
        <!-- Profile & Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
                <div class="mx-auto bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    <?php echo strtoupper(substr($guest['first_name'], 0, 1) . substr($guest['last_name'], 0, 1)); ?>
                </div>
                <h4 class="fw-bold mb-1"><?php echo $guest['first_name'] . " " . $guest['last_name']; ?></h4>
                <p class="text-muted small">Registered on <?php echo date('F d, Y', strtotime($guest['created_at'])); ?></p>
                
                <div class="text-start border-top pt-4 mt-2">
                    <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                    <p class="mb-3 text-dark"><?php echo $guest['email']; ?></p>
                    
                    <label class="form-label small fw-bold text-muted text-uppercase">Contact Number</label>
                    <p class="mb-0 text-dark"><?php echo $guest['phone']; ?></p>
                </div>
            </div>

            <!-- Business Intelligence Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
                <h6 class="opacity-75 small text-uppercase fw-bold mb-2">Lifetime Value (LTV)</h6>
                <h2 class="fw-bold mb-4">₱<?php echo number_format($stats['lifetime_value'] ?? 0, 2); ?></h2>
                
                <div class="row text-center g-0 border-top border-white border-opacity-25 pt-3">
                    <div class="col-4">
                        <small class="d-block opacity-75">Bookings</small>
                        <span class="fw-bold"><?php echo $stats['total_bookings']; ?></span>
                    </div>
                    <div class="col-4 border-start border-end border-white border-opacity-25">
                        <small class="d-block opacity-75">Stays</small>
                        <span class="fw-bold"><?php echo $stats['successful_stays']; ?></span>
                    </div>
                    <div class="col-4">
                        <small class="d-block opacity-75">Cancels</small>
                        <span class="fw-bold"><?php echo $stats['total_cancellations']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking & Activity History -->
        <div class="col-lg-8">
            <div class="card table-card shadow-sm border-0">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold text-dark m-0">Recent Activity & Stays</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-4">Reference</th>
                                <th>Room & Type</th>
                                <th>Schedule</th>
                                <th>Amount</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $history_sql = "SELECT b.*, r.room_number, rt.type_name 
                                            FROM bookings b 
                                            JOIN rooms r ON b.room_id = r.room_id 
                                            JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                            WHERE b.guest_id = '$guest_id' 
                                            ORDER BY b.check_in_date DESC";
                            $history_res = $conn->query($history_sql);

                            if($history_res->num_rows > 0):
                                while($row = $history_res->fetch_assoc()):
                                    // Status Badge Color Logic
                                    $st_class = 'badge-soft-secondary';
                                    if($row['status'] == 'confirmed') $st_class = 'bg-success bg-opacity-10 text-success';
                                    if($row['status'] == 'checked_in') $st_class = 'bg-info bg-opacity-10 text-info';
                                    if($row['status'] == 'checked_out') $st_class = 'bg-dark bg-opacity-10 text-dark';
                                    if($row['status'] == 'cancelled') $st_class = 'bg-danger bg-opacity-10 text-danger';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">#<?php echo $row['reference_code']; ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold">Room <?php echo $row['room_number']; ?></div>
                                    <small class="text-muted"><?php echo $row['type_name']; ?></small>
                                </td>
                                <td>
                                    <div class="small fw-medium"><?php echo date('M d', strtotime($row['check_in_date'])); ?> - <?php echo date('M d, Y', strtotime($row['check_out_date'])); ?></div>
                                    <small class="text-muted"><?php echo $row['nights']; ?> Night(s)</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">₱<?php echo number_format($row['total_amount'], 2); ?></span>
                                </td>
                                <td class="pe-4">
                                    <span class="badge rounded-pill text-capitalize <?php echo $st_class; ?> px-3 py-2">
                                        <?php echo str_replace('_', ' ', $row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                    No booking history available for this guest.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to permanently remove this guest? This will not delete their historical bookings but will restrict account access.')) {
        window.location.href = 'process_delete_guest.php?id=' + id;
    }
}
</script>

<?php include 'components/footer.php'; ?>