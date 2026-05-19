<?php 
session_start();
include 'config/db.php'; 

// 1. Security: Only allow logged-in Guests (Role 4)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch Detailed Guest Profile
$user_query = "SELECT u.email, g.* FROM users u 
               JOIN guests g ON u.user_id = g.user_id 
               WHERE u.user_id = '$user_id' LIMIT 1";
$user_res = $conn->query($user_query);
$profile = $user_res->fetch_assoc();
$guest_id = $profile['guest_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | IslaView Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        
        .navbar { background: var(--deep-ocean) !important; padding: 12px 0; }
        
        /* Profile Sidebar Styling */
        .profile-card {
            background: white;
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }
        .profile-header {
            background: var(--primary-blue);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 15px;
            border: 4px solid rgba(255,255,255,0.3);
        }

        /* Booking Card Styling */
        .booking-card { 
            border: none; 
            border-radius: 15px; 
            background: white; 
            transition: 0.3s; 
            border-left: 5px solid transparent;
        }
        .booking-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        .status-pending { border-left-color: #ffc107; }
        .status-confirmed { border-left-color: #198754; }
        .status-checked_in { border-left-color: #0dcaf0; }
        .status-checked_out { border-left-color: #6c757d; }
        .status-cancelled { border-left-color: #dc3545; }

        .ref-code { font-family: 'Courier New', Courier, monospace; font-weight: 700; color: var(--primary-blue); }
        .payment-badge { font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-islands me-2"></i>ISLAVIEW
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="index.php"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card profile-card shadow-sm border-0 mb-4">
                <div class="profile-header">
                    <div class="profile-avatar shadow-sm">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h4>
                    <p class="small opacity-75 mb-0">Valued IslaView Guest</p>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Contact Details</h6>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-envelope text-primary me-3 fs-5"></i>
                        <div>
                            <div class="small text-muted">Email Address</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($profile['email']); ?></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-telephone text-primary me-3 fs-5"></i>
                        <div>
                            <div class="small text-muted">Phone Number</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($profile['phone'] ?: 'Not Set'); ?></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-geo-alt text-primary me-3 fs-5"></i>
                        <div>
                            <div class="small text-muted">Primary Address</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($profile['address'] ?: 'No address provided'); ?></div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold" onclick="alert('Update Profile feature coming soon!')">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
                <h5 class="fw-bold">Ready for a new trip?</h5>
                <p class="small opacity-75">Book another stay and enjoy the luxury of Talibon.</p>
                <a href="index.php#rooms" class="btn btn-light btn-sm rounded-pill fw-bold px-4">Browse Rooms</a>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0">My Reservation History</h3>
                <span class="badge bg-white text-dark border rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-clock-history me-1"></i> Recent Activity
                </span>
            </div>

            <?php
            $sql = "SELECT b.*, rt.type_name, r.room_number, p.status as payment_status 
                    FROM bookings b 
                    JOIN rooms r ON b.room_id = r.room_id 
                    JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                    LEFT JOIN payments p ON b.booking_id = p.booking_id
                    WHERE b.guest_id = '$guest_id' 
                    ORDER BY b.created_at DESC";
            
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $status = $row['status'];
                    $pay_status = $row['payment_status'] ?? 'unpaid';
                    $card_class = "status-" . $status;
                    
                    $badge_color = "bg-secondary";
                    if($status == 'confirmed') $badge_color = "bg-success";
                    if($status == 'pending') $badge_color = "bg-warning text-dark";
                    if($status == 'checked_in') $badge_color = "bg-info text-white";
                    if($status == 'cancelled') $badge_color = "bg-danger";
            ?>
                <div class="card booking-card shadow-sm mb-4 <?php echo $card_class; ?>">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-2 text-center border-end">
                                <div class="small text-muted text-uppercase mb-1">Check-in</div>
                                <h3 class="fw-bold mb-0"><?php echo date('d', strtotime($row['check_in_date'])); ?></h3>
                                <div class="text-uppercase small fw-bold text-muted"><?php echo date('M Y', strtotime($row['check_in_date'])); ?></div>
                                <span class="badge <?php echo $badge_color; ?> mt-2 text-capitalize"><?php echo str_replace('_', ' ', $status); ?></span>
                            </div>

                            <div class="col-lg-4 ps-lg-4 mt-3 mt-lg-0">
                                <div class="small text-muted text-uppercase fw-bold mb-1">
                                    Ref: <span class="ref-code">#<?php echo $row['reference_code']; ?></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1"><?php echo $row['type_name']; ?></h5>
                                <div class="mb-2">
                                    <?php if($pay_status == 'verified'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success payment-badge px-2 py-1 rounded">
                                            <i class="bi bi-check-circle-fill me-1"></i>PAID
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning payment-badge px-2 py-1 rounded">
                                            <i class="bi bi-clock-history me-1"></i>AWAITING PAYMENT
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-calendar3 me-2"></i><?php echo date('M d', strtotime($row['check_in_date'])); ?> — <?php echo date('M d', strtotime($row['check_out_date'])); ?>
                                </p>
                            </div>

                            <div class="col-lg-3 text-lg-center mt-3 mt-lg-0">
                                <div class="small text-muted">Total Amount</div>
                                <h4 class="fw-bold text-primary mb-0">₱<?php echo number_format($row['total_amount'], 2); ?></h4>
                                <small class="text-muted"><?php echo $row['nights']; ?> Night(s)</small>
                            </div>

                            <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="view_itinerary.php?ref=<?php echo $row['reference_code']; ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    
                                    <?php if($status == 'pending'): ?>
                                        <button onclick="cancelBooking('<?php echo $row['reference_code']; ?>')" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                            Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile; 
            else: 
            ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3">No Reservations Yet</h5>
                    <p class="text-muted">Your booking history will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelBooking(ref) {
    if(confirm('Are you sure you want to cancel this reservation?')) {
        window.location.href = 'cancel_booking.php?ref=' + ref;
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>