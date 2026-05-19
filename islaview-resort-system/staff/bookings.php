<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Reservations</h2>
            <p class="text-muted">Manage guest bookings and arrival schedules.</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center col-md-7 justify-content-end">
            <a href="walkin_booking.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-none d-md-flex align-items-center">
                <i class="bi bi-person-plus-fill me-2"></i> New Walk-in
            </a>

            <div class="col-md-7">
                <form action="bookings.php" method="GET">
                    <?php if(isset($_GET['status'])): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($_GET['status']); ?>">
                    <?php endif; ?>
                    
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0" placeholder="Guest Name or Ref Code..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        
                        <?php if(!empty($_GET['search'])): ?>
                            <a href="bookings.php<?php echo isset($_GET['status']) ? '?status='.$_GET['status'] : ''; ?>" class="btn bg-white border-0 text-muted"><i class="bi bi-x-circle"></i></a>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex gap-2 overflow-auto">
                <?php 
                    // Function to build URLs while keeping the search parameter alive
                    function filterUrl($status = null) {
                        $params = $_GET;
                        if ($status) $params['status'] = $status; else unset($params['status']);
                        return "bookings.php?" . http_build_query($params);
                    }
                ?>
                <a href="<?php echo filterUrl(); ?>" class="btn btn-sm <?php echo !isset($_GET['status']) ? 'btn-primary' : 'btn-light'; ?> rounded-pill px-3">All Bookings</a>
                <a href="<?php echo filterUrl('pending'); ?>" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'pending' ? 'btn-warning text-dark' : 'btn-light'; ?> rounded-pill px-3">Pending</a>
                <a href="<?php echo filterUrl('confirmed'); ?>" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'confirmed' ? 'btn-success' : 'btn-light'; ?> rounded-pill px-3">Confirmed</a>
                <a href="<?php echo filterUrl('checked_in'); ?>" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'checked_in' ? 'btn-info text-white' : 'btn-light'; ?> rounded-pill px-3">Checked In</a>
                <a href="<?php echo filterUrl('checked_out'); ?>" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'checked_out' ? 'btn-dark' : 'btn-light'; ?> rounded-pill px-3">Checked Out</a>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Reference</th>
                        <th>Guest Details</th>
                        <th>Room</th>
                        <th>Stay Schedule</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL Query building
                    $sql = "SELECT b.*, g.first_name, g.last_name, g.phone, r.room_number, rt.type_name 
                            FROM bookings b 
                            JOIN guests g ON b.guest_id = g.guest_id 
                            JOIN rooms r ON b.room_id = r.room_id
                            JOIN room_types rt ON r.room_type_id = rt.room_type_id";

                    $where = [];
                    // Filter by Status
                    if (isset($_GET['status']) && !empty($_GET['status'])) {
                        $st = mysqli_real_escape_string($conn, $_GET['status']);
                        $where[] = "b.status = '$st'";
                    }
                    // Search Logic (Ref Code, Name, or Phone)
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($conn, $_GET['search']);
                        $where[] = "(b.reference_code LIKE '%$search%' 
                                    OR g.first_name LIKE '%$search%' 
                                    OR g.last_name LIKE '%$search%' 
                                    OR g.phone LIKE '%$search%')";
                    }

                    if (count($where) > 0) {
                        $sql .= " WHERE " . implode(" AND ", $where);
                    }

                    $sql .= " ORDER BY b.created_at DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Badge Styling
                            $badge_class = 'bg-secondary';
                            if($row['status'] == 'confirmed') $badge_class = 'bg-success';
                            elseif($row['status'] == 'pending') $badge_class = 'bg-warning text-dark';
                            elseif($row['status'] == 'checked_in') $badge_class = 'bg-info text-white';
                            elseif($row['status'] == 'checked_out') $badge_class = 'bg-dark';
                            elseif($row['status'] == 'cancelled') $badge_class = 'bg-danger';

                            echo "<tr>
                                <td class='ps-4'>
                                    <span class='fw-bold text-primary'>#{$row['reference_code']}</span><br>
                                    <small class='text-muted'>".date('M d, Y', strtotime($row['created_at']))."</small>
                                </td>
                                <td>
                                    <div class='fw-bold text-dark'>{$row['first_name']} {$row['last_name']}</div>
                                    <small class='text-muted'><i class='bi bi-telephone me-1'></i>{$row['phone']}</small>
                                </td>
                                <td>
                                    <div class='small fw-bold'>Room {$row['room_number']}</div>
                                    <small class='text-muted text-uppercase' style='font-size: 0.7rem;'>{$row['type_name']}</small>
                                </td>
                                <td>
                                    <div class='small text-dark fw-medium'>".date('M d', strtotime($row['check_in_date']))." - ".date('M d, Y', strtotime($row['check_out_date']))."</div>
                                    <small class='text-muted'>{$row['nights']} Night(s)</small>
                                </td>
                                <td>
                                    <span class='badge $badge_class rounded-pill text-capitalize px-3'>".str_replace('_', ' ', $row['status'])."</span>
                                </td>
                                <td class='pe-4 text-end'>
                                    <a href='view_booking.php?id={$row['booking_id']}' class='btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold'>
                                        Manage
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'>
                                <i class='bi bi-search fs-1 d-block mb-2 opacity-25'></i>
                                No reservations found matching your criteria.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>