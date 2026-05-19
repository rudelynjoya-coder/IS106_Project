<?php
session_start();
include '../config/db.php';

/**
 * SECURITY CHECK
 * Dapat Role 1 (Superadmin) o Role 2 (Admin) lang ang may access dito.
 * Ang Role 3 (Staff) ay bawal mag-manage ng ibang staff accounts.
 */
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];

    // --- 1. ACTION: ADD NEW STAFF ---
    if ($action == 'add_staff') {
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $lname = mysqli_real_escape_string($conn, $_POST['lname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role_id = mysqli_real_escape_string($conn, $_POST['role_id']);
        $password = $_POST['password'];

        // I-check kung ang email ay registered na
        $check_email = $conn->query("SELECT email FROM users WHERE email = '$email'");

        if ($check_email->num_rows > 0) {
            echo "<script>alert('Error: Ang email na ito ay gamit na.'); window.history.back();</script>";
        } else {
            // Hash password gamit ang Default (Bcrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (role_id, first_name, last_name, email, password_hash, is_active, email_verified, created_at) 
                    VALUES ('$role_id', '$fname', '$lname', '$email', '$hashed_password', 1, 1, NOW())";

            if ($conn->query($sql)) {
                echo "<script>alert('Staff account created successfully!'); window.location='staff_management.php';</script>";
            } else {
                echo "<script>alert('Error: Database insert failed.'); window.history.back();</script>";
            }
        }
    }

    // --- 2. ACTION: EDIT STAFF DETAILS ---
    elseif ($action == 'edit_staff') {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $fname   = mysqli_real_escape_string($conn, $_POST['fname']);
        $lname   = mysqli_real_escape_string($conn, $_POST['lname']);
        $email   = mysqli_real_escape_string($conn, $_POST['email']);
        $role_id = mysqli_real_escape_string($conn, $_POST['role_id']);

        $sql_update = "UPDATE users SET 
                        first_name = '$fname', 
                        last_name = '$lname', 
                        email = '$email', 
                        role_id = '$role_id',
                        updated_at = NOW() 
                       WHERE user_id = '$user_id'";

        if ($conn->query($sql_update)) {
            echo "<script>alert('Staff information updated!'); window.location='staff_management.php';</script>";
        } else {
            echo "<script>alert('Update failed.'); window.history.back();</script>";
        }
    }

    // --- 3. ACTION: TOGGLE STATUS (Active/Inactive) ---
    elseif ($action == 'toggle_status') {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $current_status = $_POST['current_status'];
        
        // Pagbaligtarin ang status (1 to 0, or 0 to 1)
        $new_status = ($current_status == 1) ? 0 : 1;

        $sql_toggle = "UPDATE users SET is_active = '$new_status' WHERE user_id = '$user_id'";
        
        if ($conn->query($sql_toggle)) {
            // Dahil button ito sa table, redirect lang agad para seamless
            header("Location: staff_management.php");
        }
    }

    // --- 4. ACTION: DELETE STAFF ---
    elseif ($action == 'delete_staff') {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

        // SECURITY: Bawal i-delete ng Admin ang sarili niyang account
        if ($user_id == $_SESSION['user_id']) {
            echo "<script>alert('Security Error: You cannot delete your own account!'); window.location='staff_management.php';</script>";
            exit();
        }

        $sql_delete = "DELETE FROM users WHERE user_id = '$user_id'";

        if ($conn->query($sql_delete)) {
            echo "<script>alert('Staff member removed from system.'); window.location='staff_management.php';</script>";
        } else {
            echo "<script>alert('Delete failed. User might be linked to other records.'); window.location='staff_management.php';</script>";
        }
    }

} else {
    // Balik sa management page kung walang valid action
    header("Location: staff_management.php");
    exit();
}