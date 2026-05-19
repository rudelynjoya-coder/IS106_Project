<?php
// Sinisigurong may session bago mag-check ng role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * SECURITY CHECK:
 * 1 = Superadmin, 2 = Admin
 * Role 3 pataas (Staff/Guest) ay hindi pinapayagan dito base sa logic ng admin area.
 */
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) {
    header("Location: ../index.php");
    exit();
}

// Database Connection
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | IslaView Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --primary-blue: #0077b6; --deep-ocean: #03045e; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        .navbar { background: rgba(3, 4, 94, 0.95) !important; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .nav-link { font-weight: 500; transition: 0.3s; }
        .nav-link:hover { color: #caf0f8 !important; }
        .card-stats { border: none; border-radius: 20px; transition: transform 0.3s ease; }
        .card-stats:hover { transform: translateY(-5px); }
        .icon-box { width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .main-container { margin-top: 100px; padding-bottom: 50px; }
        .table-card { border: none; border-radius: 20px; overflow: hidden; }
        
        /* Karagdagang utility classes para sa tables */
        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            color: #6c757d;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
        }
    </style>
</head>
<body>