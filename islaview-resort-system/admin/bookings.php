<?php 
include 'components/header.php'; 
include 'components/navbar.php'; 
?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Reservation Management</h2>
            <p class="text-muted">Monitor and update guest bookings here.</p>
        </div>
        <button class="btn btn-primary btn-book shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#walkInModal">
            <i class="bi bi-plus-lg me-2"></i>New Walk-in
        </button>
    </div>

    <!-- Booking Filters -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Reference or Guest Name..." value="<?php echo $_GET['search'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="checked_in" <?php echo (isset($_GET['status']) && $_GET['status'] == 'checked_in') ? 'selected' : ''; ?>>Checked In</option>
                        <option value="checked_out" <?php echo (isset($_GET['status']) && $_GET['status'] == 'checked_out') ? 'selected' : ''; ?>>Checked Out</option>
                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control bg-light border-0" title="Filter by Check-in Date" value="<?php echo $_GET['date'] ?? ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card table-card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Ref Code</th>
                            <th>Guest</th>
                            <th>Room Type</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th class="pe-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Base SQL Query
                        $sql = "SELECT b.*, g.first_name, g.last_name, rt.type_name 
                                FROM bookings b 
                                JOIN guests g ON b.guest_id = g.guest_id 
                                JOIN rooms r ON b.room_id = r.room_id
                                JOIN room_types rt ON r.room_type_id = rt.room_type_id";

                        // Apply Dynamic Filters
                        $conditions = [];
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search']);
                            $conditions[] = "(b.reference_code LIKE '%$search%' OR g.first_name LIKE '%$search%' OR g.last_name LIKE '%$search%')";
                        }
                        if (isset($_GET['status']) && !empty($_GET['status'])) {
                            $status = mysqli_real_escape_string($conn, $_GET['status']);
                            $conditions[] = "b.status = '$status'";
                        }
                        if (isset($_GET['date']) && !empty($_GET['date'])) {
                            $date = mysqli_real_escape_string($conn, $_GET['date']);
                            $conditions[] = "b.check_in_date = '$date'";
                        }

                        if (count($conditions) > 0) {
                            $sql .= " WHERE " . implode(' AND ', $conditions);
                        }

                        $sql .= " ORDER BY b.created_at DESC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                // Status Badge Styling
                                $badge_color = 'bg-secondary';
                                if($row['status'] == 'confirmed') $badge_color = 'bg-success';
                                elseif($row['status'] == 'pending') $badge_color = 'bg-warning text-dark';
                                elseif($row['status'] == 'checked_in') $badge_color = 'bg-info text-white';
                                elseif($row['status'] == 'checked_out') $badge_color = 'bg-dark text-white';
                                elseif($row['status'] == 'cancelled') $badge_color = 'bg-danger';

                                echo "<tr>
                                    <td class='ps-4 fw-bold text-primary'>#".$row['reference_code']."</td>
                                    <td>
                                        <div class='fw-bold'>".$row['first_name']." ".$row['last_name']."</div>
                                        <small class='badge bg-light text-muted fw-normal' style='font-size: 0.7rem;'>".strtoupper($row['booking_source'])."</small>
                                    </td>
                                    <td><span class='small fw-medium text-secondary'>".$row['type_name']."</span></td>
                                    <td>
                                        <div class='small'><i class='bi bi-calendar-check me-1 text-primary'></i>".date('M d', strtotime($row['check_in_date']))."</div>
                                        <div class='small text-muted'><i class='bi bi-calendar-x me-1'></i>".date('M d, Y', strtotime($row['check_out_date']))."</div>
                                    </td>
                                    <td><span class='badge $badge_color px-3 rounded-pill text-capitalize' style='min-width: 90px;'>".$row['status']."</span></td>
                                    <td class='fw-bold text-dark'>₱".number_format($row['total_amount'], 2)."</td>
                                    <td class='pe-4 text-center'>
                                        <div class='btn-group shadow-sm rounded-3 overflow-hidden border'>
                                            <a href='view_booking.php?id=".$row['booking_id']."' class='btn btn-sm btn-white px-3' title='View Details'>
                                                <i class='bi bi-eye text-primary'></i>
                                            </a>
                                            <a href='view_booking.php?id=".$row['booking_id']."' class='btn btn-sm btn-white px-3 border-start' title='Edit / Update'>
                                                <i class='bi bi-pencil-square text-secondary'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                                    <i class='bi bi-inbox fs-1 d-block mb-3 opacity-25'></i>
                                    No records found matching your criteria.
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: New Walk-in Reservation -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h4 class="fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>Walk-in Reservation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="alert alert-info border-0 rounded-4 small text-start">
                    <i class="bi bi-info-circle-fill me-2"></i> For manual bookings, please ensure the Guest is already registered in the system or create a new account for them first.
                </div>
                <p class="text-muted py-4">Manual booking form implementation is pending...</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="walkin_booking.php" class="btn btn-primary rounded-pill px-4">Proceed to Form</a>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>