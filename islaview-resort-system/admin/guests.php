<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<?php
// ACTION: DELETE GUEST
if (isset($_POST['delete_guest_id'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_POST['delete_guest_id']);
    // Note: This will only work if there are no foreign key constraints preventing deletion.
    if ($conn->query("DELETE FROM users WHERE user_id = '$id_to_delete' AND role_id = 4")) {
        echo "<script>alert('Guest account deleted successfully.'); window.location='guests.php';</script>";
    } else {
        echo "<script>alert('Error: Cannot delete guest. They might have existing bookings.');</script>";
    }
}
?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Guest Directory</h2>
            <p class="text-muted small">Manage registered guests and view their profiles.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print List
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">Total Registered</h6>
                        <?php 
                            $total_g = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 4")->fetch_assoc();
                            echo "<h4 class='fw-bold mb-0'>".$total_g['total']."</h4>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">VIP Guests</h6>
                        <?php 
                            $vip_g = $conn->query("SELECT COUNT(*) as total FROM guests WHERE is_vip = 1")->fetch_assoc();
                            echo "<h4 class='fw-bold mb-0'>".$vip_g['total']."</h4>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">New This Month</h6>
                        <?php 
                            $curr_month = date('m');
                            $curr_year = date('Y');
                            $month_g = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id = 4 AND MONTH(created_at) = '$curr_month' AND YEAR(created_at) = '$curr_year'")->fetch_assoc();
                            echo "<h4 class='fw-bold mb-0'>".$month_g['total']."</h4>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-bold">Guest Records</h5>
                </div>
                <div class="col-auto">
                    <form action="guests.php" method="GET" class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" name="search" class="form-control border-end-0" placeholder="Name, Email, or ID..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <a href="guests.php" class="btn btn-outline-secondary border-start-0 border-end-0 bg-white text-muted"><i class="bi bi-x-circle"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Guest Name</th>
                            <th>Contact Info</th>
                            <th>Status</th>
                            <th>Member Since</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // SEARCH LOGIC
                        $where_sql = "WHERE u.role_id = 4";
                        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                            $s = mysqli_real_escape_string($conn, trim($_GET['search']));
                            $where_sql .= " AND (u.first_name LIKE '%$s%' 
                                            OR u.last_name LIKE '%$s%' 
                                            OR u.email LIKE '%$s%' 
                                            OR u.user_id LIKE '%$s%')";
                        }

                        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.created_at, g.is_vip 
                                FROM users u 
                                LEFT JOIN guests g ON u.user_id = g.user_id 
                                $where_sql 
                                ORDER BY u.created_at DESC";
                        
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $full_name = $row['first_name'] . " " . $row['last_name'];
                                $initials = strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1));
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 0.8rem; font-weight: bold; border: 1px solid rgba(0,119,182,0.2);">
                                                <?php echo $initials; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $full_name; ?></div>
                                                <small class="text-muted">ID: #GS-<?php echo $row['user_id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small"><i class="bi bi-envelope me-2 text-muted"></i><?php echo $row['email']; ?></div>
                                        <div class="small text-muted"><i class="bi bi-telephone me-2 text-muted"></i><?php echo $row['phone'] ?? 'No Phone'; ?></div>
                                    </td>
                                    <td>
                                        <?php if($row['is_vip']): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-gem me-1"></i> VIP</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted rounded-pill px-3 border">Regular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="view_guest_history.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-light border rounded-pill px-3" title="View History">
                                                <i class="bi bi-journal-text text-primary me-1"></i> History
                                            </a>
                                            
                                            <form action="guests.php" method="POST" onsubmit="return confirm('Delete this guest account forever?')">
                                                <input type="hidden" name="delete_guest_id" value="<?php echo $row['user_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle" title="Delete Account">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                    <i class='bi bi-person-x fs-1 d-block mb-2 opacity-25'></i>
                                    No guests found matching your criteria.
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>