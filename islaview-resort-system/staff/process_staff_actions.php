<?php
session_start();
include '../config/db.php';

// Security Check: Siguraduhin na Staff (Role 3) lamang ang makaka-access sa file na ito
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['booking_id'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $action = $_POST['action'];
    $staff_id = $_SESSION['user_id'];

    // 1. Kunin ang Room ID at Reference Code mula sa database para sa logging at status update
    $get_info = $conn->query("SELECT room_id, reference_code FROM bookings WHERE booking_id = '$booking_id'");
    
    if ($get_info->num_rows == 0) {
        echo "<script>alert('Error: Booking record not found.'); window.location='bookings.php';</script>";
        exit();
    }
    
    $b_data = $get_info->fetch_assoc();
    $room_id = $b_data['room_id'];
    $ref_code = $b_data['reference_code'];

    // Initialize variables para sigurado na may laman bago gamitin sa queries
    $log_msg = "";
    $new_room_status = "";
    $new_booking_status = "";

    // Simulan ang Database Transaction para sigurado na sabay mag-uupdate ang lahat ng tables
    $conn->begin_transaction();

    try {
        if ($action == 'check_in') {
            $new_booking_status = 'checked_in';
            $new_room_status = 'occupied';
            $log_msg = "Staff confirmed check-in for Ref #$ref_code";

        } elseif ($action == 'check_out') {
            $new_booking_status = 'checked_out';
            $new_room_status = 'cleaning'; // Standard procedure: cleaning muna bago maging available ulit
            $log_msg = "Staff confirmed check-out for Ref #$ref_code. Room set to cleaning.";

        } elseif ($action == 'cancel') {
            $new_booking_status = 'cancelled';
            $new_room_status = 'available';
            $log_msg = "Staff cancelled booking Ref #$ref_code";
        }

        // A. I-update ang Status sa Bookings Table
        $sql_booking = "UPDATE bookings SET status = '$new_booking_status', updated_at = NOW() WHERE booking_id = '$booking_id'";
        if (!$conn->query($sql_booking)) {
            throw new Exception("Failed to update booking status.");
        }
        
        // B. I-update ang Current Status ng Kwarto
        $sql_room = "UPDATE room_current_status SET status = '$new_room_status', updated_at = NOW() WHERE room_id = '$room_id'";
        if (!$conn->query($sql_room)) {
            throw new Exception("Failed to update room status.");
        }

        // C. Mag-insert sa room_status_log (Matches your database structure)
        $sql_log = "INSERT INTO room_status_log (room_id, status, changed_by, notes, changed_at) 
                    VALUES ('$room_id', '$new_room_status', '$staff_id', '$log_msg', NOW())";
        if (!$conn->query($sql_log)) {
            throw new Exception("Failed to record status log.");
        }

        // Kapag matagumpay ang lahat ng queries, i-save na sa database permanently
        $conn->commit();

        // 3. I-redirect pabalik sa details page para makita agad ang pagbabago
        header("Location: view_booking.php?id=$booking_id&msg=updated");
        exit();

    } catch (Exception $e) {
        // Kapag may kahit isang error, i-cancel lahat ng queries sa taas (Rollback)
        $conn->rollback();
        $error_msg = addslashes($e->getMessage());
        echo "<script>alert('Database Error: $error_msg'); window.history.back();</script>";
        exit();
    }

} else {
    // Kapag tinangkang i-access ang file nang hindi dumaan sa POST form
    header("Location: bookings.php");
    exit();
}