<?php 
include 'components/header.php'; 
include 'components/navbar.php'; 
?>

<main class="container main-container">
    <div class="mb-4">
        <a href="bookings.php" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Reservations
        </a>
        <h2 class="fw-bold text-dark mt-2">Walk-in Reservation</h2>
        <p class="text-muted">Register a guest and assign a room immediately.</p>
    </div>

    <form action="process_walkin.php" method="POST">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-badge me-2 text-primary"></i>Guest Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">SEARCH EXISTING GUEST</label>
                        <input class="form-control bg-light border-0" list="guestList" name="guest_id" placeholder="Type guest name..." required>
                        <datalist id="guestList">
                            <?php
                            $guests = $conn->query("SELECT guest_id, first_name, last_name FROM guests");
                            while($g = $guests->fetch_assoc()):
                                echo "<option value='{$g['guest_id']}'>{$g['first_name']} {$g['last_name']}</option>";
                            endwhile;
                            ?>
                        </datalist>
                        <div class="form-text mt-2">If guest is not found, please register them in the <a href="guests.php">Guest Directory</a> first.</div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ADULTS</label>
                            <input type="number" name="adults" class="form-control bg-light border-0" value="1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">CHILDREN</label>
                            <input type="number" name="children" class="form-control bg-light border-0" value="0" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4"><i class="bi bi-calendar-event me-2 text-primary"></i>Stay Details</h5>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">CHECK-IN</label>
                            <input type="date" name="check_in" class="form-control bg-light border-0" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">CHECK-OUT</label>
                            <input type="date" name="check_out" class="form-control bg-light border-0" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">AVAILABLE ROOM</label>
                        <select name="room_id" class="form-select bg-light border-0" required>
                            <option value="">-- Select Available Room --</option>
                            <?php
                            $rooms = $conn->query("SELECT r.room_id, r.room_number, rt.type_name, rt.base_price 
                                                 FROM rooms r 
                                                 JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                                 JOIN room_current_status rcs ON r.room_id = rcs.room_id 
                                                 WHERE rcs.status = 'available' AND r.is_active = 1");
                            while($rm = $rooms->fetch_assoc()):
                                echo "<option value='{$rm['room_id']}'>Room {$rm['room_number']} - {$rm['type_name']} (₱".number_format($rm['base_price']).")</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">RESERVATION NOTES</label>
                        <textarea name="notes" class="form-control bg-light border-0" rows="2" placeholder="Dietary needs, bed preference, etc."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Walk-in bookings are automatically <strong>Confirmed</strong>.
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                            CREATE RESERVATION
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<?php include 'components/footer.php'; ?>