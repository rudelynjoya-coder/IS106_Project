<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Room Management</h2>
            <p class="text-muted">Manage your resort inventory and pricing.</p>
        </div>
        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
            <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addRoomTypeModal">
                <i class="bi bi-tags me-2"></i>Add Type
            </button>
            <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="bi bi-plus-lg me-2"></i>Add Room
            </button>
        </div>
    </div>

    <ul class="nav nav-pills mb-4 gap-2" id="roomTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#pills-rooms">
                Individual Rooms
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#pills-types">
                Room Categories & Pricing
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-rooms">
            <div class="card table-card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Room #</th>
                                    <th>Category</th>
                                    <th>Floor</th>
                                    <th>Current Status</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_rooms = "SELECT r.*, rt.type_name, rcs.status 
                                              FROM rooms r 
                                              JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                              LEFT JOIN room_current_status rcs ON r.room_id = rcs.room_id";
                                $res_rooms = $conn->query($sql_rooms);

                                if ($res_rooms && $res_rooms->num_rows > 0) {
                                    while($row = $res_rooms->fetch_assoc()) {
                                        $status_bg = 'bg-success';
                                        if($row['status'] == 'occupied') $status_bg = 'bg-danger';
                                        if($row['status'] == 'cleaning') $status_bg = 'bg-warning text-dark';
                                        if($row['status'] == 'maintenance') $status_bg = 'bg-dark';

                                        echo "<tr>
                                            <td class='ps-4 fw-bold'>Room ".$row['room_number']."</td>
                                            <td><span class='text-primary fw-medium'>".$row['type_name']."</span></td>
                                            <td>Floor ".$row['floor_level']."</td>
                                            <td><span class='badge $status_bg px-3 rounded-pill text-capitalize'>".($row['status'] ?? 'available')."</span></td>
                                            <td class='pe-4 text-end'>
                                                <form action='process_room.php' method='POST' class='d-inline' onsubmit='return confirm(\"Delete this room?\")'>
                                                    <input type='hidden' name='action' value='delete_room'>
                                                    <input type='hidden' name='room_id' value='".$row['room_id']."'>
                                                    <button type='submit' class='btn btn-sm btn-outline-danger rounded-circle'><i class='bi bi-trash'></i></button>
                                                </form>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4'>No rooms added yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-types">
            <div class="row g-4">
                <?php
                $sql_types = "SELECT * FROM room_types";
                $res_types = $conn->query($sql_types);

                if ($res_types && $res_types->num_rows > 0) {
                    while($type = $res_types->fetch_assoc()) {
                        ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="fw-bold mb-0 text-truncate" style="max-width: 70%;"><?php echo $type['type_name']; ?></h5>
                                        <span class="badge bg-light text-primary rounded-pill border">₱<?php echo number_format($type['base_price'], 0); ?></span>
                                    </div>
                                    <p class="text-muted small" style="height: 40px; overflow: hidden;"><?php echo $type['description']; ?></p>
                                    <div class="d-flex gap-3 mb-3 text-muted small">
                                        <span><i class="bi bi-people"></i> Max <?php echo $type['max_occupancy']; ?></span>
                                        <span><i class="bi bi-circle-fill <?php echo ($type['is_active']) ? 'text-success' : 'text-danger'; ?>" style="font-size: 8px;"></i> <?php echo ($type['is_active']) ? 'Active' : 'Inactive'; ?></span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-light w-100 rounded-pill btn-sm fw-bold" 
                                                data-bs-toggle="modal" data-bs-target="#editTypeModal<?php echo $type['room_type_id']; ?>">
                                            Edit
                                        </button>
                                        <form action="process_room.php" method="POST" class="w-100" onsubmit="return confirm('Delete this category?')">
                                            <input type="hidden" name="action" value="delete_type">
                                            <input type="hidden" name="type_id" value="<?php echo $type['room_type_id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-bold">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editTypeModal<?php echo $type['room_type_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-0">
                                        <h5 class="fw-bold">Edit Category: <?php echo $type['type_name']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form action="process_room.php" method="POST">
                                            <input type="hidden" name="action" value="edit_type">
                                            <input type="hidden" name="type_id" value="<?php echo $type['room_type_id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Category Name</label>
                                                <input type="text" name="type_name" class="form-control" value="<?php echo $type['type_name']; ?>" required>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label class="form-label small fw-bold">Base Price</label>
                                                    <input type="number" name="base_price" class="form-control" value="<?php echo $type['base_price']; ?>" required>
                                                </div>
                                                <div class="col">
                                                    <label class="form-label small fw-bold">Max Guests</label>
                                                    <input type="number" name="max_occupancy" class="form-control" value="<?php echo $type['max_occupancy']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Availability Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" <?php echo $type['is_active'] ? 'selected' : ''; ?>>Active (Display on Site)</option>
                                                    <option value="0" <?php echo !$type['is_active'] ? 'selected' : ''; ?>>Inactive (Hidden)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?php echo $type['description']; ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Update Category</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRoomTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Add Room Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="process_room.php" method="POST">
                    <input type="hidden" name="action" value="add_type">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category Name</label>
                        <input type="text" name="type_name" class="form-control" placeholder="e.g. Deluxe Sea View" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">Base Price</label>
                            <input type="number" name="base_price" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Max Guests</label>
                            <input type="number" name="max_occupancy" class="form-control" value="2" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tell us about this room..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Save Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="process_room.php" method="POST">
                    <input type="hidden" name="action" value="add_room">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Room Number / Name</label>
                        <input type="text" name="room_number" class="form-control" placeholder="e.g. 101 or Villa A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="room_type_id" class="form-select" required>
                            <option value="" selected disabled>Select Category</option>
                            <?php
                            $cat_res = $conn->query("SELECT room_type_id, type_name FROM room_types WHERE is_active = 1");
                            while($cat = $cat_res->fetch_assoc()) {
                                echo "<option value='".$cat['room_type_id']."'>".$cat['type_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Floor Level</label>
                        <input type="number" name="floor_level" class="form-control" value="1" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">Register Room</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>