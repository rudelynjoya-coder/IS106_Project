<?php 
session_start();
include 'config/db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IslaView Resort | Luxury in Talibon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --primary-blue: #0077b6; 
            --deep-ocean: #03045e;
            --soft-sky: #caf0f8; 
        }

        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            scroll-behavior: smooth;
        }
        
        /* Navbar Styling */
        .navbar {
            transition: all 0.4s;
            padding: 15px 0;
            z-index: 1050;
        }
        .navbar.scrolled {
            background: rgba(3, 4, 94, 0.95) !important;
            padding: 10px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 90vh;
            min-height: 500px;
            color: white;
            display: flex;
            align-items: center;
        }

        /* Room Cards */
        .card-room {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            position: relative;
        }
        .card-room:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        .room-img-wrapper {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        .room-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .card-room:hover .room-img-wrapper img {
            transform: scale(1.1);
        }

        /* Availability Badge */
        .availability-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* Amenities Styling */
        .amenity-card {
            padding: 30px;
            border-radius: 20px;
            background: white;
            border: none;
            transition: 0.3s;
            text-align: center;
        }
        .amenity-card:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-5px);
        }
        .amenity-card i {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
            display: block;
        }
        .amenity-card:hover i { color: white; }

        .btn-primary { background-color: var(--primary-blue); border: none; border-radius: 10px; }
        .btn-primary:hover { background-color: var(--deep-ocean); }
        .btn-book { border-radius: 50px; padding: 10px 25px; font-weight: 600; }

        .modal-content { border-radius: 20px; border: none; }
        .form-control { border-radius: 10px; padding: 12px; }

        @media (max-width: 768px) {
            .display-2 { font-size: 2.5rem; }
            .hero-section { height: 70vh; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <i class="bi bi-islands me-2"></i>ISLAVIEW
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#rooms">Rooms</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#amenities">Amenities</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle btn btn-primary text-white px-4 shadow-sm" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['fname'] ?? 'Guest'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 animate slideIn">
                            <?php 
                            $role = $_SESSION['role_id'] ?? null;
                            if($role == 4): ?>
                                <li><a class="dropdown-item" href="my_bookings.php"><i class="bi bi-calendar-event me-2"></i>My Bookings</a></li>
                            <?php elseif($role == 1 || $role == 2): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            <?php elseif($role == 3): ?>
                                <li><a class="dropdown-item" href="staff/dashboard.php"><i class="bi bi-person-badge me-2"></i>Staff Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <button class="btn btn-link text-white text-decoration-none px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary btn-book ms-lg-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#signupModal">Join Now</button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container text-center text-lg-start">
        <div class="row">
            <div class="col-lg-7">
                <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill">TALIBON'S PREMIER DESTINATION</span>
                <h1 class="display-2 fw-bold mb-3" style="letter-spacing: -2px;">Escape to a <span class="text-info">Tropical Paradise</span></h1>
                <p class="lead mb-5 opacity-75">Discover unparalleled comfort, breathtaking views, and the genuine warmth of Boholano hospitality.</p>
                <div class="d-grid d-md-flex gap-3">
                    <a href="#rooms" class="btn btn-primary btn-lg btn-book px-5 shadow">Book Your Stay</a>
                    <a href="#amenities" class="btn btn-outline-light btn-lg btn-book px-5">Explore Amenities</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container my-5 py-5" id="rooms">
    <div class="text-center mb-5">
        <h6 class="text-primary fw-bold text-uppercase">Accommodations</h6>
        <h2 class="display-5 fw-bold">Available Rooms & Suites</h2>
        <div class="mx-auto bg-primary rounded" style="height: 4px; width: 60px;"></div>
    </div>
    
    <div class="row g-4">
        <?php
        // SQL WITH AVAILABILITY COUNT
        // Binibilang natin ang 'available' rooms sa bawat type mula sa room_current_status table
        $sql = "SELECT rt.*, 
                (SELECT COUNT(*) FROM rooms r 
                 JOIN room_current_status rcs ON r.room_id = rcs.room_id 
                 WHERE r.room_type_id = rt.room_type_id AND rcs.status = 'available') as available_count
                FROM room_types rt 
                WHERE rt.is_active = 1";
        
        $result = $conn->query($sql);
        $room_images = [
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=800',
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=800',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=800',
            'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=800'
        ];
        
        $i = 0;
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $img = $room_images[$i % count($room_images)];
                $avail = $row['available_count'];
                
                // Logic for availability tag
                if ($avail > 0) {
                    $tag_html = '<span class="availability-tag bg-success text-white">'.$avail.' Rooms Left</span>';
                    $btn_html = '<a href="booking.php?id='.$row['room_type_id'].'" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Book This Room</a>';
                } else {
                    $tag_html = '<span class="availability-tag bg-danger text-white">Fully Booked</span>';
                    $btn_html = '<button class="btn btn-secondary rounded-pill w-100 fw-bold py-2 disabled">Unavailable</button>';
                }

                echo '<div class="col-md-6 col-lg-4">
                    <div class="card card-room shadow-sm h-100 border">
                        '.$tag_html.'
                        <div class="room-img-wrapper">
                            <img src="'.$img.'" alt="'.$row['type_name'].'">
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="fw-bold mb-0">'.$row['type_name'].'</h4>
                                <span class="text-primary fw-bold">₱'.number_format($row['base_price']).'<small class="text-muted fw-normal" style="font-size:0.7rem">/night</small></span>
                            </div>
                            <p class="text-muted small mb-4" style="height: 45px; overflow: hidden;">'.$row['description'].'</p>
                            <div class="row g-2 mb-4 text-center">
                                <div class="col-4 border-end"><i class="bi bi-people text-primary small"></i><div style="font-size:0.7rem">'.$row['max_occupancy'].' Pax</div></div>
                                <div class="col-4 border-end"><i class="bi bi-wifi text-primary small"></i><div style="font-size:0.7rem">Free Wi-Fi</div></div>
                                <div class="col-4"><i class="bi bi-snow text-primary small"></i><div style="font-size:0.7rem">AC</div></div>
                            </div>
                            '.$btn_html.'
                        </div>
                    </div>
                </div>';
                $i++;
            }
        }
        ?>
    </div>
</div>

<div class="bg-light py-5" id="amenities">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase">Facilities</h6>
            <h2 class="display-5 fw-bold">Amenities & Services</h2>
            <div class="mx-auto bg-primary" style="height: 4px; width: 50px;"></div>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="amenity-card shadow-sm"><i class="bi bi-water"></i><h5 class="fw-bold">Infinity Pool</h5></div>
            </div>
            <div class="col-md-3">
                <div class="amenity-card shadow-sm"><i class="bi bi-wifi"></i><h5 class="fw-bold">Fast Wi-Fi</h5></div>
            </div>
            <div class="col-md-3">
                <div class="amenity-card shadow-sm"><i class="bi bi-cup-hot"></i><h5 class="fw-bold">Dining</h5></div>
            </div>
            <div class="col-md-3">
                <div class="amenity-card shadow-sm"><i class="bi bi-p-square"></i><h5 class="fw-bold">Parking</h5></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0"><h3 class="fw-bold text-primary">Welcome Back</h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="auth_guest.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3"><label class="small fw-bold mb-1">Email Address</label><input type="email" name="email" class="form-control bg-light border-0" required></div>
                    <div class="mb-4"><label class="small fw-bold mb-1">Password</label><input type="password" name="password" class="form-control bg-light border-0" required></div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">Sign In</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="signupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0"><h3 class="fw-bold text-success">Create Account</h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="auth_guest.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="row mb-3">
                        <div class="col"><label class="small fw-bold mb-1">First Name</label><input type="text" name="fname" class="form-control bg-light border-0" required></div>
                        <div class="col"><label class="small fw-bold mb-1">Last Name</label><input type="text" name="lname" class="form-control bg-light border-0" required></div>
                    </div>
                    <div class="mb-3"><label class="small fw-bold mb-1">Email</label><input type="email" name="email" class="form-control bg-light border-0" required></div>
                    <div class="mb-3"><label class="small fw-bold mb-1">Phone</label><input type="text" name="phone" class="form-control bg-light border-0" required></div>
                    <div class="mb-4"><label class="small fw-bold mb-1">Password</label><input type="password" name="password" class="form-control bg-light border-0" required></div>
                    <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm">Join IslaView</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-5 mt-5">
    <div class="container text-center">
        <h4 class="fw-bold" style="letter-spacing:1px">ISLAVIEW RESORT</h4>
        <p class="opacity-50 small mb-0">Talibon, Bohol, Philippines</p>
        <p class="opacity-50 small">Experience Luxury, Experience Bohol.</p>
        <hr class="opacity-25 my-4 mx-auto" style="max-width:300px">
        <p class="small opacity-50 mb-0">&copy; 2026 IslaView Resort Management System. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('scroll', function() {
        var nav = document.querySelector('.navbar');
        window.scrollY > 50 ? nav.classList.add('scrolled', 'shadow') : nav.classList.remove('scrolled', 'shadow');
    });
</script>
</body>
</html>