<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Room Inventory</h2>
            <p class="text-muted">Detailed list of all resort rooms and their current operational status.</p>
        </div>
        <div class="text-end">
            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                <a href="rooms.php" class="btn btn-white btn-sm border-end">Grid View</a>
                <a href="rooms_list.php" class="btn btn-white btn-sm active">List View</a>
            </div>
        </div>
    </div>

    <!-- Filter/Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-8 d-flex gap-2">
                    <a href="rooms_list.php" class="btn btn-sm <?php echo !isset($_GET['status']) ? 'btn-primary' : 'btn-light'; ?> rounded-pill px-3">All</a>
                    <a href="rooms_list.php?status=available" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'available' ? 'btn-success' : 'btn-light'; ?> rounded-pill px-3">Available</a>
                    <a href="rooms_list.php?status=cleaning" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'cleaning' ? 'btn-warning text-dark' : 'btn-light'; ?> rounded-pill px-3">Cleaning</a>
                    <a href="rooms_list.php?status=occupied" class="btn btn-sm <?php echo ($_GET['status'] ?? '') == 'occupied' ? 'btn-danger' : 'btn-light'; ?> rounded-pill px-3">Occupied</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Room List Table -->
    <div class="card table-card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Room #</th>
                        <th>Room Type</th>
                        <th>Current Status</th>
                        <th>Last Updated</th>
                        <th class="pe-4 text-end">Update Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT r.*, rt.type_name, rcs.status, rcs.updated_at 
                            FROM rooms r 
                            JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                            JOIN room_current_status rcs ON r.room_id = rcs.room_id";

                    if (isset($_GET['status']) && !empty($_GET['status'])) {
                        $st = mysqli_real_escape_string($conn, $_GET['status']);
                        $sql .= " WHERE rcs.status = '$st'";
                    }

                    $sql .= " ORDER BY r.room_number ASC";
                    $res = $conn->query($sql);

                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                            // Color Badge Logic
                            $badge_class = 'bg-success';
                            if($row['status'] == 'occupied') $badge_class = 'bg-danger';
                            if($row['status'] == 'cleaning') $badge_class = 'bg-warning text-dark';
                            if($row['status'] == 'maintenance') $badge_class = 'bg-dark text-white';
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold fs-5 text-dark">Room <?php echo $row['room_number']; ?></td>
                        <td>
                            <span class="text-muted fw-medium"><?php echo $row['type_name']; ?></span>
                        </td>
                        <td>
                            <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-2 text-capitalize">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark"><?php echo date('M d, Y', strtotime($row['updated_at'])); ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?php echo date('h:i A', strtotime($row['updated_at'])); ?></div>
                        </td>
                        <td class="pe-4 text-end">
                            <!-- Direct Action Form -->
                            <form action="process_room_update.php" method="POST" class="d-inline-block">
                                <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border">
                                    <select name="new_status" class="form-select border-0 px-3" style="min-width: 120px;">
                                        <option value="available" <?php if($row['status']=='available') echo 'selected'; ?>>Available</option>
                                        <option value="cleaning" <?php if($row['status']=='cleaning') echo 'selected'; ?>>Cleaning</option>
                                        <option value="maintenance" <?php if($row['status']=='maintenance') echo 'selected'; ?>>Maintenance</option>
                                        <option value="occupied" <?php if($row['status']=='occupied') echo 'selected'; ?>>Occupied</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary border-0 px-3">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No rooms match this criteria.</td></tr>";
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>