<?php
session_start();
include '../config/db.php';

// Security Check: Admin roles only
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- 1. ADD ROOM TYPE ---
    if ($action == 'add_type') {
        $type_name     = mysqli_real_escape_string($conn, $_POST['type_name']);
        $base_price    = mysqli_real_escape_string($conn, $_POST['base_price']);
        $max_occupancy = mysqli_real_escape_string($conn, $_POST['max_occupancy']);
        $description   = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "INSERT INTO room_types (type_name, base_price, max_occupancy, description, is_active) 
                VALUES ('$type_name', '$base_price', '$max_occupancy', '$description', 1)";

        if ($conn->query($sql)) {
            echo "<script>alert('New room category added!'); window.location='rooms.php';</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.location='rooms.php';</script>";
        }
    }

    // --- 2. EDIT ROOM TYPE (Ito ang kailangan mo) ---
    elseif ($action == 'edit_type') {
        $type_id       = mysqli_real_escape_string($conn, $_POST['type_id']);
        $type_name     = mysqli_real_escape_string($conn, $_POST['type_name']);
        $base_price    = mysqli_real_escape_string($conn, $_POST['base_price']);
        $max_occupancy = mysqli_real_escape_string($conn, $_POST['max_occupancy']);
        $description   = mysqli_real_escape_string($conn, $_POST['description']);
        $is_active     = mysqli_real_escape_string($conn, $_POST['is_active']);

        $sql = "UPDATE room_types SET 
                type_name = '$type_name', 
                base_price = '$base_price', 
                max_occupancy = '$max_occupancy', 
                description = '$description',
                is_active = '$is_active'
                WHERE room_type_id = '$type_id'";

        if ($conn->query($sql)) {
            echo "<script>alert('Category updated successfully!'); window.location='rooms.php';</script>";
        } else {
            echo "<script>alert('Update failed: " . addslashes($conn->error) . "'); window.location='rooms.php';</script>";
        }
    }

    // --- 3. ADD INDIVIDUAL ROOM ---
    elseif ($action == 'add_room') {
        $room_number  = mysqli_real_escape_string($conn, $_POST['room_number']);
        $room_type_id = mysqli_real_escape_string($conn, $_POST['room_type_id']);
        $floor_level  = mysqli_real_escape_string($conn, $_POST['floor_level']);

        $conn->begin_transaction();
        try {
            $sql_room = "INSERT INTO rooms (room_type_id, room_number, floor_level, is_active) 
                         VALUES ('$room_type_id', '$room_number', '$floor_level', 1)";
            $conn->query($sql_room);
            $new_room_id = $conn->insert_id;

            $conn->query("INSERT INTO room_current_status (room_id, status, updated_at) 
                          VALUES ('$new_room_id', 'available', NOW())");

            $conn->commit();
            echo "<script>alert('Room $room_number registered!'); window.location='rooms.php';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='rooms.php';</script>";
        }
    }

    // --- 4. DELETE ROOM ---
    elseif ($action == 'delete_room') {
        $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
        if ($conn->query("DELETE FROM rooms WHERE room_id = '$room_id'")) {
            echo "<script>alert('Room deleted!'); window.location='rooms.php';</script>";
        }
    }

    // --- 5. DELETE TYPE ---
    elseif ($action == 'delete_type') {
        $type_id = mysqli_real_escape_string($conn, $_POST['type_id']);
        if ($conn->query("DELETE FROM room_types WHERE room_type_id = '$type_id'")) {
            echo "<script>alert('Category deleted!'); window.location='rooms.php';</script>";
        } else {
            echo "<script>alert('Cannot delete: Category is currently in use.'); window.location='rooms.php';</script>";
        }
    }

} else {
    header("Location: rooms.php");
    exit();
}
?>