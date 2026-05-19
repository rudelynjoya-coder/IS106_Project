<?php
session_start();
include '../config/db.php';

// Security Check: Dapat Admin/Staff lang ang may access
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 3) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $admin_id = $_SESSION['user_id'];

    // 1. Kunin muna ang room_id para sa booking na ito
    $get_room = $conn->query("SELECT room_id FROM bookings WHERE booking_id = '$booking_id'");
    $room_data = $get_room->fetch_assoc();
    $room_id = $room_data['room_id'];

    // 2. Simulan ang Transaction para sigurado ang data integrity
    $conn->begin_transaction();

    try {
        // Update Booking Status
        $sql_update_booking = "UPDATE bookings SET status = '$new_status', updated_at = NOW() WHERE booking_id = '$booking_id'";
        $conn->query($sql_update_booking);

        // 3. Room Status Logic base sa New Booking Status
        $room_status = 'available'; // default

        if ($new_status == 'checked_in') {
            $room_status = 'occupied';
        } elseif ($new_status == 'checked_out') {
            $room_status = 'cleaning'; // Pag check-out, gawin munang cleaning
        } elseif ($new_status == 'cancelled' || $new_status == 'no_show') {
            $room_status = 'available';
        } else {
            // Para sa 'pending' or 'confirmed', panatilihin ang current status or available
            $room_status = 'available';
        }

        // Update Room Current Status
        $sql_update_room = "UPDATE room_current_status SET status = '$room_status', updated_at = NOW() WHERE room_id = '$room_id'";
        $conn->query($sql_update_room);

        // 4. Mag-log sa room_status_log (Audit Trail)
        $notes = "Updated via booking status change to $new_status";
        $sql_log = "INSERT INTO room_status_log (room_id, status, changed_by, notes, changed_at) 
                    VALUES ('$room_id', '$room_status', '$admin_id', '$notes', NOW())";
        $conn->query($sql_log);

        // Commit changes
        $conn->commit();

        echo "<script>
                alert('Booking and Room status successfully updated to $new_status.');
                window.location='view_booking.php?id=$booking_id';
              </script>";

    } catch (Exception $e) {
        // Rollback kung may error
        $conn->rollback();
        echo "<script>
                alert('Error updating status: " . $e->getMessage() . "');
                window.location='view_booking.php?id=$booking_id';
              </script>";
    }

} else {
    header("Location: bookings.php");
    exit();
}
?>