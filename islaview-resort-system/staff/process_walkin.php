<?php
session_start();
include '../config/db.php';

// Security Check: Staff or Admin access only
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2, 3])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Capture and Clean Inputs
    $fname     = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname     = mysqli_real_escape_string($conn, $_POST['lname']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $room_id   = mysqli_real_escape_string($conn, $_POST['room_id']);
    $check_in  = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);
    $staff_id  = $_SESSION['user_id'];

    // 2. Generate Unique Reference Code
    $ref_code = "IVR-WALK-" . strtoupper(substr(md5(uniqid()), 0, 6));

    // 3. Calculate Nights and Total Amount
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $nights = $date2->diff($date1)->format("%a");
    
    if ($nights <= 0) { $nights = 1; } // Minimum 1 night stay

    // Kunin ang presyo ng kwarto
    $room_query = $conn->query("SELECT rt.base_price FROM rooms r JOIN room_types rt ON r.room_type_id = rt.room_type_id WHERE r.room_id = '$room_id'");
    $room_data = $room_query->fetch_assoc();
    $base_rate = $room_data['base_price'];
    
    $total_base = $base_rate * $nights;
    $tax = $total_base * 0.12; // 12% VAT
    $grand_total = $total_base + $tax;

    // SIMULAN ANG DATABASE TRANSACTION
    $conn->begin_transaction();

    try {
        // STEP A: Insert Guest Details
        $sql_guest = "INSERT INTO guests (first_name, last_name, email, phone, created_at) 
                      VALUES ('$fname', '$lname', '$email', '$phone', NOW())";
        if (!$conn->query($sql_guest)) { throw new Exception("Failed to save guest info."); }
        $new_guest_id = $conn->insert_id;

        // STEP B: Insert Booking Details (Status is set to 'confirmed' directly)
        $sql_booking = "INSERT INTO bookings (
                            reference_code, guest_id, room_id, booked_by, 
                            check_in_date, check_out_date, nights, 
                            base_amount, tax_amount, total_amount, 
                            status, booking_source, created_at
                        ) VALUES (
                            '$ref_code', '$new_guest_id', '$room_id', '$staff_id', 
                            '$check_in', '$check_out', '$nights', 
                            '$total_base', '$tax', '$grand_total', 
                            'confirmed', 'walk_in', NOW()
                        )";
        if (!$conn->query($sql_booking)) { throw new Exception("Failed to create booking."); }
        $new_booking_id = $conn->insert_id;

        // STEP C: Initial Payment Record (Mark as Pending Cash)
        $sql_payment = "INSERT INTO payments (booking_id, processed_by, payment_method, amount, status, payment_type, created_at) 
                        VALUES ('$new_booking_id', '$staff_id', 'cash', '$grand_total', 'pending', 'full_payment', NOW())";
        $conn->query($sql_payment);

        // STEP D: Update Room Status Log
        $conn->query("INSERT INTO room_status_log (room_id, status, changed_by, notes, changed_at) 
                      VALUES ('$room_id', 'available', '$staff_id', 'Reserved for Walk-in #$ref_code', NOW())");

        $conn->commit();

        echo "<script>
                alert('Walk-in Booking Created! Reference: $ref_code');
                window.location='view_booking.php?id=$new_booking_id';
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }

} else {
    header("Location: walkin_booking.php");
    exit();
}