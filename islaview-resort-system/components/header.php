<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IslaView Resort | Your Tropical Escape</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Montserrat & Playfair Display -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent-gold: #c5a059;
            --ocean-blue: #0077b6;
            --dark-text: #2b2d42;
        }
        body { font-family: 'Montserrat', sans-serif; color: var(--dark-text); }
        h1, h2, h3, .playfair { font-family: 'Playfair Display', serif; }

        /* Transparent Navbar that turns solid on scroll */
        .navbar-guest {
            transition: all 0.4s ease;
            padding: 20px 0;
        }
        .navbar-guest.scrolled {
            background: white !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 10px 0;
        }
        .nav-link { font-weight: 600; color: white !important; margin: 0 15px; }
        .navbar-guest.scrolled .nav-link { color: var(--dark-text) !important; }
        
        /* Hero Section */
        .hero {
            height: 90vh;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                        url('https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .booking-bar {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .btn-reserve {
            background: var(--accent-gold);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: 0.3s;
        }
        .btn-reserve:hover { background: #b08d4a; color: white; transform: scale(1.05); }
    </style>
</head>
<body>