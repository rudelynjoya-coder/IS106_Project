<?php
session_start();
include '../config/db.php';

/**
 * SECURITY CHECK:
 * Strictly Role 3 (Staff) only.
 */
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_id'])) {
    
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $staff_id = $_SESSION['user_id'];

    // Start transaction for data integrity
    $conn->begin_transaction();

    try {
        // 1. Update the current status of the room
        $sql_update = "UPDATE room_current_status 
                       SET status = '$new_status', 
                           updated_at = NOW() 
                       WHERE room_id = '$room_id'";
        
        if (!$conn->query($sql_update)) {
            throw new Exception("Failed to update room status.");
        }

        // 2. Add an entry to the room_status_log for auditing
        // This tracks which staff member made the change
        $notes = "Manual status update by staff via dashboard.";
        $sql_log = "INSERT INTO room_status_log (room_id, status, changed_by, notes, changed_at) 
                    VALUES ('$room_id', '$new_status', '$staff_id', '$notes', NOW())";
        
        if (!$conn->query($sql_log)) {
            throw new Exception("Failed to record audit log.");
        }

        // Commit all changes
        $conn->commit();

        // Redirect back with success message
        echo "<script>
                alert('Room status successfully updated to " . ucfirst($new_status) . ".');
                window.location='rooms.php';
              </script>";

    } catch (Exception $e) {
        // Undo changes if any step fails
        $conn->rollback();
        echo "<script>
                alert('Error: " . $e->getMessage() . "');
                window.location='rooms.php';
              </script>";
    }

} else {
    // If someone tries to access this file directly via URL
    header("Location: rooms.php");
    exit();
}
?>