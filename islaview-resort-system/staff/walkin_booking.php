<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<main class="container main-container">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">New Walk-in Reservation</h2>
        <p class="text-muted">Fill out the guest details and assign a room manually.</p>
    </div>

    <form action="process_walkin.php" method="POST">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-plus me-2"></i>Guest Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">First Name</label>
                            <input type="text" name="fname" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Last Name</label>
                            <input type="text" name="lname" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold">Email (Optional)</label>
                            <input type="email" name="email" class="form-control bg-light border-0">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-light border-0" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-door-open me-2"></i>Stay Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Check-in Date</label>
                            <input type="date" name="check_in" class="form-control bg-light border-0" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Check-out Date</label>
                            <input type="date" name="check_out" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold">Select Available Room</label>
                            <select name="room_id" class="form-select bg-light border-0" required>
                                <option value="">-- Select Room --</option>
                                <?php
                                // Kunin lang ang mga kwartong 'available' base sa room_current_status
                                $rooms = $conn->query("SELECT r.room_id, r.room_number, rt.type_name, rt.base_price 
                                                     FROM rooms r 
                                                     JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                                     JOIN room_current_status rcs ON r.room_id = rcs.room_id 
                                                     WHERE rcs.status = 'available'");
                                while($rm = $rooms->fetch_assoc()) {
                                    echo "<option value='{$rm['room_id']}'>Room {$rm['room_number']} - {$rm['type_name']} (₱{$rm['base_price']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                    Create Walk-in Booking
                </button>
            </div>
        </div>
    </form>
</main>

<?php include 'components/footer.php'; ?>