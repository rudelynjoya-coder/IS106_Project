<?php
session_start();
include '../config/db.php';

// Security Check: Siguraduhin na Staff (Role 3) lamang ang makaka-access
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id']) && isset($_POST['new_status'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $staff_id   = $_SESSION['user_id'];

    // 1. Kunin muna ang Room ID at Reference Code para sa logging at inventory update
    $get_info = $conn->query("SELECT room_id, reference_code FROM bookings WHERE booking_id = '$booking_id'");
    
    if ($get_info->num_rows == 0) {
        echo "<script>alert('Error: Booking record not found.'); window.location='bookings.php';</script>";
        exit();
    }
    
    $data = $get_info->fetch_assoc();
    $room_id  = $data['room_id'];
    $ref_code = $data['reference_code'];

    // Simulan ang Database Transaction
    $conn->begin_transaction();

    try {
        // 2. I-update ang Status sa Bookings Table
        $sql_update_booking = "UPDATE bookings SET status = '$new_status', updated_at = NOW() WHERE booking_id = '$booking_id'";
        if (!$conn->query($sql_update_booking)) {
            throw new Exception("Failed to update booking status.");
        }

        // 3. LOGIC PARA SA ROOM INVENTORY
        // Base sa bagong status, ano dapat ang maging status ng actual room?
        $room_status = "";
        
        switch ($new_status) {
            case 'checked_in':
                $room_status = 'occupied';
                break;
            case 'checked_out':
                $room_status = 'cleaning'; // Standard: cleaning muna bago maging available
                break;
            case 'cancelled':
            case 'no_show':
                $room_status = 'available';
                break;
            case 'confirmed':
            case 'pending':
                // Sa 'confirmed/pending', nananatiling available ang room sa status table 
                // pero blocked na siya sa calendar logic.
                $room_status = 'available'; 
                break;
            default:
                $room_status = 'available';
        }

        // I-update ang actual room status
        if ($room_status != "") {
            $conn->query("UPDATE room_current_status SET status = '$room_status', updated_at = NOW() WHERE room_id = '$room_id'");
        }

        // 4. MAG-IWAN NG AUDIT LOG (Para sa traceability)
        $log_notes = "Manual status override to " . strtoupper($new_status) . " by Staff ID $staff_id";
        $sql_log = "INSERT INTO room_status_log (room_id, status, changed_by, notes, changed_at) 
                    VALUES ('$room_id', '$room_status', '$staff_id', '$log_notes', NOW())";
        $conn->query($sql_log);

        // I-save ang lahat ng changes
        $conn->commit();

        echo "<script>
                alert('SUCCESS: Booking #$ref_code is now " . strtoupper(str_replace('_', ' ', $new_status)) . ".');
                window.location='view_booking.php?id=$booking_id';
              </script>";

    } catch (Exception $e) {
        // Bawiin ang changes kung may error
        $conn->rollback();
        $error = mysqli_real_escape_string($conn, $e->getMessage());
        echo "<script>
                alert('DATABASE ERROR: $error');
                window.history.back();
              </script>";
    }

} else {
    // Kapag inaccess ang file nang hindi dumaan sa POST form
    header("Location: bookings.php");
    exit();
}
?>