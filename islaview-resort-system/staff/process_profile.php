<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'];

    if ($action == 'update_info') {
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $lname = mysqli_real_escape_string($conn, $_POST['lname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);

        $sql = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', phone='$phone' WHERE user_id='$user_id'";
        
        if ($conn->query($sql)) {
            $_SESSION['fname'] = $fname; // Update session name instantly
            echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
        }
    } 
    elseif ($action == 'change_password') {
        $curr_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $conf_pass = $_POST['confirm_password'];

        $res = $conn->query("SELECT password_hash FROM users WHERE user_id='$user_id'");
        $user = $res->fetch_assoc();

        if (password_verify($curr_pass, $user['password_hash'])) {
            if ($new_pass === $conf_pass) {
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
                $conn->query("UPDATE users SET password_hash='$hashed' WHERE user_id='$user_id'");
                echo "<script>alert('Password changed successfully!'); window.location='profile.php';</script>";
            } else {
                echo "<script>alert('New passwords do not match!'); window.location='profile.php';</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect!'); window.location='profile.php';</script>";
        }
    }
}
?>