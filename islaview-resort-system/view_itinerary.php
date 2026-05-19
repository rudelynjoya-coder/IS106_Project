<?php 
session_start();
include 'config/db.php'; 

// 1. Security Check: Must be logged in and a reference must be provided
if (!isset($_SESSION['user_id']) || !isset($_GET['ref'])) {
    header("Location: index.php");
    exit();
}

$ref_code = mysqli_real_escape_string($conn, $_GET['ref']);
$user_id = $_SESSION['user_id'];

// 2. Fetch Detailed Booking Info - Joining 5 tables to include Payment Status
$sql = "SELECT b.*, rt.type_name, rt.description as room_desc, r.room_number, 
        g.first_name, g.last_name, g.email, g.phone,
        p.status as payment_status, p.payment_method, p.transaction_ref
        FROM bookings b
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.reference_code = '$ref_code' AND g.user_id = '$user_id'
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>alert('Itinerary not found.'); window.location='my_bookings.php';</script>";
    exit();
}

$data = $result->fetch_assoc();
$is_paid = ($data['payment_status'] == 'verified');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itinerary #<?php echo $ref_code; ?> | IslaView</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; padding: 40px 0; }
        
        .itinerary-paper { 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            max-width: 850px; 
            margin: auto;
            border-top: 8px solid var(--deep-ocean);
            position: relative;
            overflow: hidden;
        }
        
        /* Payment Watermark */
        .payment-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 900;
            opacity: 0.05;
            pointer-events: none;
            text-transform: uppercase;
            z-index: 0;
            width: 100%;
            text-align: center;
        }

        .voucher-header { border-bottom: 2px solid #f8f9fa; padding-bottom: 20px; position: relative; z-index: 1; }
        .info-label { font-size: 0.75rem; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 1px; }
        .info-value { font-weight: 600; color: #333; }
        
        .status-stamp {
            border: 3px solid;
            padding: 8px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 800;
            text-transform: uppercase;
            transform: rotate(-10deg);
            opacity: 0.8;
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .stamp-paid { color: #198754; border-color: #198754; }
        .stamp-unpaid { color: #dc3545; border-color: #dc3545; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .itinerary-paper { box-shadow: none; width: 100%; border-radius: 0; border-top: 5px solid var(--deep-ocean); }
            .container { width: 100%; max-width: 100%; }
            .payment-watermark { opacity: 0.03; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-center no-print" style="max-width: 850px; margin: auto;">
        <a href="my_bookings.php" class="btn btn-link text-decoration-none text-muted p-0">
            <i class="bi bi-arrow-left me-1"></i> Back to My Bookings
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer me-2"></i> Print Itinerary
        </button>
    </div>

    <div class="itinerary-paper p-5">
        <div class="payment-watermark">
            <?php echo $is_paid ? 'PAID' : 'UNPAID'; ?>
        </div>

        <div class="voucher-header d-flex justify-content-between align-items-start mb-5">
            <div>
                <h1 class="fw-bold text-primary mb-0" style="letter-spacing: -1px;">ISLAVIEW</h1>
                <p class="text-muted small fw-bold">Resort & Leisure Park • Talibon, Bohol</p>
                <div class="mt-2 text-muted small">
                    <i class="bi bi-geo-alt-fill me-1"></i> Brgy. San San, Talibon, Bohol, Philippines
                </div>
            </div>
            <div class="text-end">
                <div class="status-stamp <?php echo $is_paid ? 'stamp-paid' : 'stamp-unpaid'; ?>">
                    <?php echo $is_paid ? 'FULLY PAID' : 'PENDING PAYMENT'; ?>
                </div>
                <?php if($is_paid): ?>
                    <div class="small text-muted mt-2">Ref: <?php echo $data['transaction_ref']; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-5" style="position: relative; z-index: 1;">
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="info-label">Primary Guest</div>
                    <div class="info-value fs-5"><?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?></div>
                </div>
                <div class="mb-4">
                    <div class="info-label">Booking Reference</div>
                    <div class="info-value text-primary fs-5">#<?php echo $data['reference_code']; ?></div>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="mb-4">
                    <div class="info-label">Confirmation Date</div>
                    <div class="info-value"><?php echo date('M d, Y', strtotime($data['created_at'])); ?></div>
                </div>
                <div class="mb-4">
                    <div class="info-label">Contact Details</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['email']); ?></div>
                    <div class="info-value small"><?php echo htmlspecialchars($data['phone']); ?></div>
                </div>
            </div>

            <div class="col-12">
                <div class="bg-light rounded-4 p-4 d-flex justify-content-around text-center border">
                    <div>
                        <div class="info-label">Check-in</div>
                        <div class="info-value fs-5"><?php echo date('D, M d, Y', strtotime($data['check_in_date'])); ?></div>
                        <small class="text-muted">Standard 2:00 PM</small>
                    </div>
                    <div class="border-start ps-4">
                        <div class="info-label">Check-out</div>
                        <div class="info-value fs-5"><?php echo date('D, M d, Y', strtotime($data['check_out_date'])); ?></div>
                        <small class="text-muted">Standard 12:00 PM</small>
                    </div>
                    <div class="border-start ps-4">
                        <div class="info-label">Total Stay</div>
                        <div class="info-value fs-5"><?php echo $data['nights']; ?> Night(s)</div>
                        <small class="text-muted"><?php echo $data['num_adults']; ?> Adult(s)</small>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-5">
                <h6 class="fw-bold mb-3 border-bottom pb-2 text-uppercase small" style="letter-spacing: 1px;">Reservation Summary</h6>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="info-value">Unit Assignment: Room <?php echo $data['room_number']; ?></div>
                        <div class="small text-muted"><?php echo $data['type_name']; ?></div>
                    </div>
                    <div class="fw-bold text-dark">₱<?php echo number_format($data['base_amount'], 2); ?></div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                    <div>Taxes & Service Fees (VAT 12%)</div>
                    <div>₱<?php echo number_format($data['tax_amount'], 2); ?></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <h5 class="fw-bold mb-0">Total Amount <?php echo $is_paid ? 'Settled' : 'Due'; ?></h5>
                    <h4 class="fw-bold text-primary mb-0">₱<?php echo number_format($data['total_amount'], 2); ?></h4>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="p-4 border rounded-4 bg-white" style="border-style: dashed !important;">
                    <h6 class="small fw-bold mb-3 text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i>TERMS AND CONDITIONS</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-1"><strong>Identity Verification:</strong> Please present this voucher and a valid Government-issued ID upon arrival.</li>
                        <li class="mb-1"><strong>Payment Status:</strong> <?php echo $is_paid ? 'This reservation is fully paid. No further room charges upon check-in.' : 'This reservation is UNPAID. Please settle the amount at the front desk before check-in.'; ?></li>
                        <li class="mb-1"><strong>Check-in/out:</strong> Standard Check-in is at 2:00 PM. Check-out is at 12:00 PM.</li>
                        <li><strong>No Show:</strong> Guests who do not arrive on the scheduled date will forfeit their reservation.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center pt-5 border-top opacity-50 small">
            <p class="mb-1 fw-bold text-uppercase">IslaView Resort Management System</p>
            <p class="mb-0">This document is electronically generated and serves as official proof of reservation.</p>
            <p>Support: support@islaviewresort.ph | Tel: +63 925 455 5278</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <p class="text-muted small">Need help? Contact our front desk at <span class="text-primary fw-bold">+63 925 455 5278</span></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>