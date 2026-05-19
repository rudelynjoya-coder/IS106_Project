<?php 
session_start();
include 'config/db.php'; 

// Check if Reference Code is provided
if (!isset($_GET['ref'])) {
    header("Location: index.php");
    exit();
}

$ref_code = mysqli_real_escape_string($conn, $_GET['ref']);

// Fetch booking details for display
$sql = "SELECT b.*, rt.type_name, r.room_number 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.room_id 
        JOIN room_types rt ON r.room_type_id = rt.room_type_id 
        WHERE b.reference_code = '$ref_code' LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmed | IslaView Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { 
            background-color: #f0f2f5; 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .success-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
            overflow: hidden; 
            background: white;
            max-width: 500px; /* Reduced width for better desktop scaling */
            margin: auto;
        }
        .success-header { 
            background: var(--deep-ocean); 
            color: white; 
            padding: 25px 20px; 
            text-align: center; 
        }
        .ref-box { 
            background: #f8f9fa; 
            border: 1.5px dashed #dee2e6; 
            border-radius: 12px; 
            padding: 15px; 
            margin: 15px 0; 
        }
        .btn-home { 
            background: var(--primary-blue); 
            color: white; 
            border-radius: 50px; 
            padding: 10px 25px; 
            font-weight: 600; 
            transition: 0.3s; 
            text-decoration: none;
        }
        .btn-home:hover { background: var(--deep-ocean); color: white; }
        .check-icon { font-size: 2.5rem; color: #2ecc71; }
        .detail-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-uppercase: uppercase; }
        .detail-value { font-size: 0.9rem; font-weight: 600; color: #333; }
    </style>
</head>
<body>

<div class="container">
    <div class="success-card">
        <!-- Compact Header -->
        <div class="success-header">
            <i class="bi bi-check-circle-fill check-icon d-block mb-2"></i>
            <h4 class="fw-bold m-0">Booking Confirmed</h4>
            <p class="small opacity-75 mb-0">We've reserved your stay at IslaView Resort</p>
        </div>
        
        <div class="card-body p-4">
            <!-- Reference Section -->
            <div class="text-center">
                <p class="small text-muted mb-1">Confirmation Reference</p>
                <div class="ref-box">
                    <h3 class="fw-bold text-primary m-0" style="letter-spacing: 1px;"><?php echo $booking['reference_code']; ?></h3>
                </div>
            </div>

            <!-- Booking Details Grid -->
            <div class="row g-3 mt-1">
                <div class="col-6">
                    <div class="detail-label">Room Category</div>
                    <div class="detail-value text-truncate"><?php echo $booking['type_name']; ?></div>
                </div>
                <div class="col-6">
                    <div class="detail-label">Room Assignment</div>
                    <div class="detail-value">Unit <?php echo $booking['room_number']; ?></div>
                </div>
                <div class="col-6">
                    <div class="detail-label">Check-in Date</div>
                    <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></div>
                </div>
                <div class="col-6">
                    <div class="detail-label">Amount Settled</div>
                    <div class="detail-value text-success">₱<?php echo number_format($booking['total_amount'], 2); ?></div>
                </div>
            </div>

            <hr class="my-4 opacity-10">

            <!-- Actions -->
            <div class="text-center">
                <a href="my_bookings.php" class="btn btn-home w-100 mb-2 shadow-sm">Manage My Bookings</a>
                <a href="index.php" class="text-muted small text-decoration-none">Return to Homepage</a>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-3">
        <p class="text-muted" style="font-size: 0.75rem;">
            <i class="bi bi-envelope-check me-1"></i> A copy of this receipt was sent to your email.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>