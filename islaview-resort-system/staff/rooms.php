<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Room Status</h2>
            <p class="text-muted">Monitor and update room availability in real-time.</p>
        </div>
        <div class="text-end">
            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                <a href="rooms.php" class="btn btn-white btn-sm active border-end">Grid View</a>
                <a href="rooms_list.php" class="btn btn-white btn-sm">List View</a>
            </div>
        </div>
    </div>

    <!-- Quick Legend -->
    <div class="d-flex gap-3 mb-4 flex-wrap">
        <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> Available</div>
        <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> Occupied</div>
        <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> Cleaning</div>
        <div class="small"><i class="bi bi-circle-fill text-dark me-1"></i> Maintenance</div>
    </div>

    <div class="row g-4">
        <?php
        $sql = "SELECT r.*, rt.type_name, rcs.status, rcs.updated_at as last_update
                FROM rooms r 
                JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                JOIN room_current_status rcs ON r.room_id = rcs.room_id
                ORDER BY r.room_number ASC";
        $res = $conn->query($sql);

        if($res->num_rows > 0):
            while($row = $res->fetch_assoc()):
                // Color Logic
                $border_color = 'border-success';
                $bg_light = 'bg-success';
                if($row['status'] == 'occupied') { $border_color = 'border-danger'; $bg_light = 'bg-danger'; }
                if($row['status'] == 'cleaning') { $border_color = 'border-warning'; $bg_light = 'bg-warning'; }
                if($row['status'] == 'maintenance') { $border_color = 'border-dark'; $bg_light = 'bg-dark'; }
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                <!-- Color Strip at the Top -->
                <div class="<?php echo $bg_light; ?> opacity-75" style="height: 6px;"></div>
                
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1"><?php echo $row['type_name']; ?></h6>
                    <h2 class="fw-bold mb-3">Room <?php echo $row['room_number']; ?></h2>
                    
                    <span class="badge <?php echo $bg_light; ?> rounded-pill px-3 py-2 text-capitalize mb-4">
                        <?php echo $row['status']; ?>
                    </span>

                    <form action="process_room_update.php" method="POST" class="mt-2">
                        <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">
                        <div class="input-group input-group-sm">
                            <select name="new_status" class="form-select border-end-0 rounded-start-pill">
                                <option value="available" <?php if($row['status']=='available') echo 'selected'; ?>>Available</option>
                                <option value="cleaning" <?php if($row['status']=='cleaning') echo 'selected'; ?>>Cleaning</option>
                                <option value="maintenance" <?php if($row['status']=='maintenance') echo 'selected'; ?>>Maintenance</option>
                            </select>
                            <button type="submit" class="btn btn-outline-primary rounded-end-pill px-3">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <small class="text-muted" style="font-size: 0.7rem;">
                            Updated: <?php echo date('h:i A', strtotime($row['last_update'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            endwhile; 
        else:
            echo "<div class='col-12 text-center py-5'><p class='text-muted'>No rooms found in the system.</p></div>";
        endif; 
        ?>
    </div>
</main>

<?php include 'components/footer.php'; ?>