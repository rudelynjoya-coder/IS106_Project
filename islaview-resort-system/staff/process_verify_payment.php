<?php
session_start();
include '../config/db.php';

// Security Check
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2, 3])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $actual_ref = $_POST['actual_ref'];
    $typed_ref  = strtoupper(trim($_POST['confirm_ref'])); // Ito ang tinype ng staff sa box
    $user_id    = $_SESSION['user_id'];

    // 1. I-check kung match ang tinype sa actual reference
    if ($typed_ref !== $actual_ref) {
        echo "<script>
                alert('MALI: Hindi tugma ang Reference Code. Pakisulat muli.');
                window.history.back();
              </script>";
        exit();
    }

    $conn->begin_transaction();

    try {
        // 2. UPDATE PAYMENTS TABLE
        // DITO NATIN ILALAGAY ANG TYPED REF SA transaction_ref COLUMN
        $sql_payment = "UPDATE payments SET 
                        status = 'verified', 
                        verified_at = NOW(), 
                        processed_by = '$user_id',
                        transaction_ref = '$typed_ref' 
                        WHERE booking_id = '$booking_id'";
        
        if (!$conn->query($sql_payment)) {
            throw new Exception("Hindi ma-update ang payment record.");
        }

        // 3. UPDATE BOOKINGS TABLE
        $sql_booking = "UPDATE bookings SET 
                        status = 'confirmed', 
                        updated_at = NOW() 
                        WHERE booking_id = '$booking_id'";

        if (!$conn->query($sql_booking)) {
            throw new Exception("Hindi ma-update ang booking status.");
        }

        $conn->commit();

        echo "<script>
                alert('SUCCESS: Verified na ang bayad para sa #$actual_ref!');
                window.location='view_booking.php?id=$booking_id';
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('System Error: " . addslashes($e->getMessage()) . "');
                window.location='view_booking.php?id=$booking_id';
              </script>";
    }

} else {
    header("Location: bookings.php");
    exit();
}
?>