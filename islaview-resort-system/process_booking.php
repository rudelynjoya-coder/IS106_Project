<?php
session_start();
include 'config/db.php';

// Security: Guest must be logged in (Role 4)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $room_type_id = mysqli_real_escape_string($conn, $_POST['room_type_id']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $num_guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 1;

    // 1. CALCULATE STAY DURATION
    $start_date = new DateTime($check_in);
    $end_date = new DateTime($check_out);
    $interval = $start_date->diff($end_date);
    $nights = $interval->days;

    if ($nights <= 0) {
        echo "<script>alert('Error: Check-out date must be after Check-in date.'); window.history.back();</script>";
        exit();
    }

    // 2. RESOLVE FOREIGN KEY CONSTRAINT (Sync Users -> Guests)
    // We check if this user already exists in the 'guests' table
    $check_guest = $conn->query("SELECT guest_id FROM guests WHERE user_id = '$user_id' LIMIT 1");
    
    if ($check_guest->num_rows > 0) {
        $g_data = $check_guest->fetch_assoc();
        $guest_id = $g_data['guest_id'];
    } else {
        // If not in guests table, we must insert them first to prevent the Constraint Error
        $user_info = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'")->fetch_assoc();
        $fname = mysqli_real_escape_string($conn, $user_info['first_name']);
        $lname = mysqli_real_escape_string($conn, $user_info['last_name']);
        $email = mysqli_real_escape_string($conn, $user_info['email']);
        $phone = mysqli_real_escape_string($conn, $user_info['phone']);
        
        $insert_guest = "INSERT INTO guests (user_id, first_name, last_name, email, phone, created_at) 
                         VALUES ('$user_id', '$fname', '$lname', '$email', '$phone', NOW())";
        
        if ($conn->query($insert_guest)) {
            $guest_id = $conn->insert_id;
        } else {
            die("Critical Error: Could not sync guest profile. " . $conn->error);
        }
    }

    // 3. FIND AN AVAILABLE ROOM
    // Matches the selected type and ensures status is 'available'
    $room_search = "SELECT r.room_id, rt.base_price 
                    FROM rooms r 
                    JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                    JOIN room_current_status rcs ON r.room_id = rcs.room_id 
                    WHERE r.room_type_id = '$room_type_id' 
                    AND rcs.status = 'available' 
                    AND r.is_active = 1
                    LIMIT 1";
    
    $room_res = $conn->query($room_search);

    if ($room_res->num_rows > 0) {
        $room_data = $room_res->fetch_assoc();
        $room_id = $room_data['room_id'];
        $base_price = $room_data['base_price'];

        // 4. FINANCIAL CALCULATIONS
        $base_amount = $base_price * $nights;
        $tax_rate = 0.12; // 12% VAT
        $tax_amount = $base_amount * $tax_rate;
        $total_amount = $base_amount + $tax_amount;
        
        $ref_code = "IVR-" . strtoupper(substr(md5(time() . $user_id), 0, 8));

        // START TRANSACTION
        $conn->begin_transaction();

        try {
            // A. Insert Booking Record
            $sql_booking = "INSERT INTO bookings (
                reference_code, guest_id, room_id, check_in_date, check_out_date, 
                num_adults, nights, base_amount, tax_amount, total_amount, 
                status, booking_source, special_requests, created_at
            ) VALUES (
                '$ref_code', '$guest_id', '$room_id', '$check_in', '$check_out', 
                '$num_guests', '$nights', '$base_amount', '$tax_amount', '$total_amount', 
                'pending', 'online', '$notes', NOW()
            )";
            $conn->query($sql_booking);
            $booking_id = $conn->insert_id;

            // B. Update Room Status to 'blocked' or 'occupied'
            $conn->query("UPDATE room_current_status SET status = 'occupied', updated_at = NOW() WHERE room_id = '$room_id'");

            // C. Log the Room Status Change (Audit Trail)
            $conn->query("INSERT INTO room_status_log (room_id, status, notes, changed_at) VALUES ('$room_id', 'occupied', 'Auto-assigned via Online Booking #$ref_code', NOW())");

            // D. Create Initial Payment Entry
            $conn->query("INSERT INTO payments (booking_id, amount, payment_method, payment_type, status, created_at) 
                          VALUES ('$booking_id', '$total_amount', 'cash', 'full_payment', 'pending', NOW())");

            $conn->commit();
            header("Location: booking_success.php?ref=$ref_code");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Database Error: " . $e->getMessage() . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No rooms available for this category. Please choose another room type.'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>