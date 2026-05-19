<?php 
include 'components/header.php'; 
include 'components/navbar.php'; 

// Extra Security: Kung ang Role ID ay 3 (Staff), bawal siya dito.
if ($_SESSION['role_id'] == 3) {
    echo "<script>alert('Access Denied: Admins only.'); window.location='dashboard.php';</script>";
    exit();
}
?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Staff Management</h2>
            <p class="text-muted">Manage system users and their access levels.</p>
        </div>
        <button class="btn btn-primary btn-book shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="bi bi-person-plus-fill me-2"></i>Add New Staff
        </button>
    </div>

    <div class="card table-card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Employee Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT u.*, r.role_name 
                                FROM users u 
                                JOIN roles r ON u.role_id = r.role_id 
                                WHERE u.role_id <= 3 
                                ORDER BY u.role_id ASC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $role_bg = ($row['role_id'] == 1) ? 'bg-danger' : (($row['role_id'] == 2) ? 'bg-primary' : 'bg-info text-dark');
                                ?>
                                <tr>
                                    <td class='ps-4'>
                                        <div class='fw-bold'><?php echo $row['first_name'] . " " . $row['last_name']; ?></div>
                                        <small class='text-muted'>ID: #EMP-<?php echo $row['user_id']; ?></small>
                                    </td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><span class='badge <?php echo $role_bg; ?> px-3 rounded-pill'><?php echo $row['role_name']; ?></span></td>
                                    <td>
                                        <form action="process_staff.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                            <button type="submit" class="btn p-0 border-0">
                                                <?php if($row['is_active']): ?>
                                                    <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                                <?php else: ?>
                                                    <span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class='pe-4 text-end'>
                                        <button class='btn btn-sm btn-outline-secondary rounded-circle me-1' 
                                                data-bs-toggle="modal" data-bs-target="#editStaffModal<?php echo $row['user_id']; ?>">
                                            <i class='bi bi-pencil'></i>
                                        </button>

                                        <?php if($_SESSION['user_id'] != $row['user_id']): ?>
                                            <form action="process_staff.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this employee?')">
                                                <input type="hidden" name="action" value="delete_staff">
                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editStaffModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-0">
                                                <h5 class="fw-bold">Edit Staff Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <form action="process_staff.php" method="POST">
                                                    <input type="hidden" name="action" value="edit_staff">
                                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="small fw-bold">First Name</label>
                                                            <input type="text" name="fname" class="form-control" value="<?php echo $row['first_name']; ?>" required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="small fw-bold">Last Name</label>
                                                            <input type="text" name="lname" class="form-control" value="<?php echo $row['last_name']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">Email Address</label>
                                                        <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="small fw-bold">Role</label>
                                                        <select name="role_id" class="form-select">
                                                            <option value="3" <?php echo ($row['role_id']==3) ? 'selected' : ''; ?>>Staff / Receptionist</option>
                                                            <option value="2" <?php echo ($row['role_id']==2) ? 'selected' : ''; ?>>Admin / Manager</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Update Employee</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Register New Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="process_staff.php" method="POST">
                    <input type="hidden" name="action" value="add_staff">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">First Name</label>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Assign Role</label>
                        <select name="role_id" class="form-select" required>
                            <option value="3">Staff / Receptionist</option>
                            <option value="2">Admin / Manager</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Initial Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Register Employee</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>