<?php 
include 'components/header.php'; 
include 'components/navbar.php'; 

// Fetch current admin details
$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'");
$user = $query->fetch_assoc();
?>

<div class="container main-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark">Account Settings</h2>
                    <p class="text-muted">Manage your personal information and security.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Profile Information Card -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Personal Details</h5>
                            <form action="process_profile.php" method="POST">
                                <input type="hidden" name="action" value="update_info">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">First Name</label>
                                        <input type="text" name="fname" class="form-control rounded-3" value="<?php echo $user['first_name']; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Last Name</label>
                                        <input type="text" name="lname" class="form-control rounded-3" value="<?php echo $user['last_name']; ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Email Address</label>
                                        <input type="email" name="email" class="form-control rounded-3" value="<?php echo $user['email']; ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted">Phone Number</label>
                                        <input type="text" name="phone" class="form-control rounded-3" value="<?php echo $user['phone']; ?>">
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security / Password Card -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Security</h5>
                            <form action="process_profile.php" method="POST">
                                <input type="hidden" name="action" value="change_password">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Current Password</label>
                                    <input type="password" name="current_password" class="form-control rounded-3" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">New Password</label>
                                    <input type="password" name="new_password" class="form-control rounded-3" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control rounded-3" required>
                                </div>
                                <button type="submit" class="btn btn-outline-dark w-100 rounded-pill fw-bold">Update Password</button>
                            </form>
                        </div>
                    </div>

                    <!-- Role Badge Info -->
                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Account Role</h6>
                                    <p class="small mb-0 opacity-75">
                                        <?php echo ($_SESSION['role_id'] == 1) ? 'Super Administrator' : 'System Administrator'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>