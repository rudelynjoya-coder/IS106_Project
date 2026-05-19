<?php
session_start();
include '../config/db.php';

/**
 * Security Check: 
 * Pinapayagan ang Role 1 (Superadmin), Role 2 (Admin), at Role 3 (Staff)
 */
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2, 3])) {
    header("Location: ../index.php");
    exit();
}

// Ginagamit natin ang POST dahil nanggagaling ito sa verification form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $actual_ref = $_POST['actual_ref'];
    $typed_ref  = strtoupper(trim($_POST['confirm_ref'])); // Kunin ang tinype na reference
    $user_id    = $_SESSION['user_id'];

    // 1. SECURITY VALIDATION: I-check kung match ang tinype sa dapat na reference
    if ($typed_ref !== $actual_ref) {
        echo "<script>
                alert('FAILED: Reference Code does not match! Please type it carefully.');
                window.history.back();
              </script>";
        exit();
    }

    // SIMULAN ANG DATABASE TRANSACTION
    $conn->begin_transaction();

    try {
        // 2. UPDATE PAYMENTS TABLE
        // I-update ang status at i-save ang tinype na reference sa transaction_ref column
        $sql_payment = "UPDATE payments SET 
                        status = 'verified', 
                        verified_at = NOW(), 
                        processed_by = '$user_id',
                        transaction_ref = '$typed_ref' 
                        WHERE booking_id = '$booking_id'";
        
        if (!$conn->query($sql_payment)) {
            throw new Exception("Unable to update payment record.");
        }

        // 3. UPDATE BOOKINGS TABLE
        // Mula 'pending', gawin nating 'confirmed'
        $sql_booking = "UPDATE bookings SET 
                        status = 'confirmed', 
                        updated_at = NOW() 
                        WHERE booking_id = '$booking_id'";

        if (!$conn->query($sql_booking)) {
            throw new Exception("Unable to update booking status.");
        }

        // 4. AUDIT LOG (Para sa traceability)
        $log_msg = "Payment verified manually by User ID $user_id for Ref #$actual_ref";
        $sql_log = "INSERT INTO audit_logs (user_id, action, table_name, record_id, created_at) 
                    VALUES ('$user_id', 'Manual Payment Verification', 'payments', '$booking_id', NOW())";
        
        $conn->query($sql_log);

        // COMMIT: I-save na ang lahat ng changes
        $conn->commit();

        echo "<script>
                alert('SUCCESS: Payment verified! Booking #$actual_ref is now confirmed.');
                window.location='view_booking.php?id=$booking_id';
              </script>";

    } catch (Exception $e) {
        // ROLLBACK: Kung may nag-error, i-cancel ang transaction
        $conn->rollback();
        $error_message = mysqli_real_escape_string($conn, $e->getMessage());
        echo "<script>
                alert('SYSTEM ERROR: $error_message');
                window.location='view_booking.php?id=$booking_id';
              </script>";
    }

} else {
    // Kapag sinubukang i-access nang diretso ang file
    header("Location: bookings.php");
    exit();
}
?>