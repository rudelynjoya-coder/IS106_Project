<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // --- GUEST REGISTRATION ---
    if ($action == 'register') {
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $lname = mysqli_real_escape_string($conn, $_POST['lname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $password = $_POST['password'];
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $role_id = 4; // Assigned as Guest

        // Check if email exists
        $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
        if ($checkEmail->num_rows > 0) {
            echo "<script>alert('Email already registered! Please login.'); window.location='index.php';</script>";
        } else {
            $sql = "INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, is_active, created_at) 
                    VALUES ('$role_id', '$fname', '$lname', '$email', '$phone', '$hashed_password', 1, NOW())";

            if ($conn->query($sql)) {
                echo "<script>alert('Registration successful! You can now sign in.'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "'); window.location='index.php';</script>";
            }
        }
    } 

    // --- UNIVERSAL LOGIN ---
    elseif ($action == 'login') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email = '$email' AND is_active = 1 LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password_hash'])) {
                // Set Global Sessions
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['fname']   = $user['first_name'];
                $_SESSION['lname']   = $user['last_name'];

                // Redirection based on role
                if ($user['role_id'] == 1 || $user['role_id'] == 2) {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role_id'] == 3) {
                    header("Location: staff/dashboard.php");
                } else {
                    // Guest stays on the landing page
                    header("Location: index.php");
                }
                exit();
                
            } else {
                echo "<script>alert('Invalid password!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('Account not found or inactive.'); window.location='index.php';</script>";
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>