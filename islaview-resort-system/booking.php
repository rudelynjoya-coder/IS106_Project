<?php 
session_start();
include 'config/db.php'; 

// 1. Check if Room ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$room_type_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Room Type Details
$sql = "SELECT * FROM room_types WHERE room_type_id = '$room_type_id' AND is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$room = $result->fetch_assoc();

// 3. Security: Check if Guest is logged in
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo $room['type_name']; ?> | IslaView Resort</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; color: #333; }
        
        /* Navbar Styling */
        .navbar { 
            background: var(--deep-ocean) !important; 
            padding: 15px 0; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Card Styling */
        .booking-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            background: white;
        }
        
        .room-preview-img { 
            width: 100%; 
            height: 250px; 
            object-fit: cover; 
            border-radius: 15px; 
        }
        
        .price-tag { 
            font-size: 1.5rem; 
            color: var(--primary-blue); 
            font-weight: 800; 
        }
        
        .sticky-sidebar { position: sticky; top: 100px; }
        
        .form-label { font-size: 0.8rem; color: #6c757d; }
        
        .form-control, .form-select {
            border: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
            transition: 0.3s;
        }
        
        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-blue);
            box-shadow: none;
        }
    </style>
</head>
<body>

<!-- Simple Header Navigation -->
<nav class="navbar navbar-dark mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-arrow-left me-2"></i> RETURN TO RESORT
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Left Column: Booking Form -->
        <div class="col-lg-7">
            <div class="card booking-card p-4 mb-4">
                <h3 class="fw-bold mb-4 text-dark">Reservation Details</h3>
                
                <?php if(!$is_logged_in): ?>
                    <!-- Authentication Alert -->
                    <div class="alert alert-warning border-0 rounded-4 p-4 mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-lock-fill fs-4 me-2"></i>
                            <h5 class="fw-bold m-0">Sign In Required</h5>
                        </div>
                        <p class="mb-3 opacity-75">To ensure a secure booking process, please log in to your guest account first.</p>
                        <a href="index.php#login" class="btn btn-primary rounded-pill px-4 fw-bold">Login to Account</a>
                    </div>
                <?php endif; ?>

                <form action="process_booking.php" method="POST" id="bookingForm">
                    <input type="hidden" name="room_type_id" value="<?php echo $room_type_id; ?>">
                    
                    <div class="row g-4">
                        <!-- Dates Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase">Check-in Date</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" required 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase">Check-out Date</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" required 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                        </div>
                        
                        <!-- Guest Count -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-uppercase">Number of Guests</label>
                            <select name="guests" class="form-select" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <?php for($i=1; $i<=$room['max_occupancy']; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Special Requests -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-uppercase">Special Requests (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Example: Early arrival, high floor, extra pillows..."
                                      <?php echo !$is_logged_in ? 'disabled' : ''; ?>></textarea>
                        </div>
                    </div>

                    <!-- Guest Identity Summary -->
                    <div class="mt-4 p-3 bg-light rounded-4 border-start border-primary border-4">
                        <h6 class="fw-bold mb-1">Booking Identity</h6>
                        <p class="text-muted small mb-0">
                            <?php if($is_logged_in): ?>
                                Confirmed as: <span class="text-dark fw-bold"><?php echo htmlspecialchars($_SESSION['fname'] . " " . $_SESSION['lname']); ?></span>
                            <?php else: ?>
                                Identity Verification: <span class="text-danger">Awaiting Login</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-3 mt-4 fw-bold rounded-pill shadow-sm" 
                            <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                        PROCEED TO CONFIRMATION
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Summary Sidebar -->
        <div class="col-lg-5">
            <div class="sticky-sidebar">
                <div class="card booking-card p-4 overflow-hidden">
                    <!-- Image Source should be dynamic if available in your DB -->
                    <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=800" 
                         class="room-preview-img mb-4" alt="Luxury Room Preview">
                    
                    <h4 class="fw-bold mb-1 text-dark"><?php echo $room['type_name']; ?></h4>
                    <p class="text-muted small mb-4"><?php echo htmlspecialchars($room['description']); ?></p>
                    
                    <hr class="opacity-10 mb-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Daily Rate</span>
                        <span class="price-tag">₱<?php echo number_format($room['base_price'], 2); ?></span>
                    </div>
                    
                    <!-- Dynamic Summary Display -->
                    <div class="p-4 rounded-4 mt-4" style="background-color: #f8f9fa; border: 1px dashed #ced4da;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Total Nights:</span>
                            <span id="night_count" class="fw-bold text-dark">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted small">Grand Total:</span>
                            <span id="total_display" class="fw-bold text-primary fs-5">₱0.00</span>
                        </div>
                        <div class="d-flex align-items-center text-muted" style="font-size: 0.7rem;">
                            <i class="bi bi-info-circle me-2"></i>
                            <span>Taxes and service fees are included in the final price.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /**
     * ISLAVIEW BOOKING CALCULATION LOGIC
     * Updates nights and total price in real-time
     */
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const nightCountDisplay = document.getElementById('night_count');
    const totalDisplay = document.getElementById('total_display');
    const basePrice = <?php echo $room['base_price']; ?>;

    function calculateStay() {
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;

        if (checkIn && checkOut) {
            const startDate = new Date(checkIn);
            const endDate = new Date(checkOut);
            
            // Calculate millisecond difference
            const diffTime = endDate - startDate;
            
            // Convert to days
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                const grandTotal = diffDays * basePrice;
                nightCountDisplay.innerText = diffDays;
                totalDisplay.innerText = "₱" + grandTotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                // Handle invalid date selections
                nightCountDisplay.innerText = "0";
                totalDisplay.innerText = "₱0.00";
            }
        }
    }

    // Event Listeners for Date Inputs
    checkInInput.addEventListener('change', () => {
        // Automatically set min check-out date to check-in + 1 day
        const nextDay = new Date(checkInInput.value);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOutInput.min = nextDay.toISOString().split('T')[0];
        calculateStay();
    });

    checkOutInput.addEventListener('change', calculateStay);
</script>
</body>
</html>