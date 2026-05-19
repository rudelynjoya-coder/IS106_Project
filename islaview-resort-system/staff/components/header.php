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

$staff_name = $_SESSION['fname'] ?? 'Staff';
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal | IslaView Resort</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        
        /* Navbar Style */
        .navbar { 
            background: rgba(3, 4, 94, 0.95) !important; 
            backdrop-filter: blur(10px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .nav-link { font-weight: 500; transition: 0.3s; color: rgba(255,255,255,0.8) !important; }
        .nav-link:hover, .nav-link.active { color: #caf0f8 !important; }
        
        /* Dashboard Stats */
        .card-stats { border: none; border-radius: 20px; transition: transform 0.3s ease; color: white; }
        .card-stats:hover { transform: translateY(-5px); }
        .icon-box { 
            width: 50px; 
            height: 50px; 
            background: rgba(255,255,255,0.2); 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
        }
        
        /* UI Elements */
        .main-container { margin-top: 100px; padding-bottom: 50px; }
        .table-card { border: none; border-radius: 20px; overflow: hidden; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .btn-checkin {
            background-color: var(--primary-blue);
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
            transition: 0.3s;
        }
        .btn-checkin:hover { background-color: var(--deep-ocean); color: white; }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            color: #6c757d;
            border: none;
            padding: 15px;
        }
    </style>
</head>
<body>