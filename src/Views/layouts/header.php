<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Tour Du Lịch</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .hero-section {
            background: linear-gradient(135deg, #0066cc, #004c99);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .tour-card {
            transition: transform 0.3s;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .tour-price {
            color: #ff6b35;
            font-size: 1.3rem;
            font-weight: bold;
        }
        footer {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-umbrella-beach"></i> BookingTour
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-home"></i> Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tour/list"><i class="fas fa-search"></i> Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="/home/about"><i class="fas fa-info-circle"></i> Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="/home/contact"><i class="fas fa-envelope"></i> Liên hệ</a></li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Đã đăng nhập -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user']['FullName']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile"><i class="fas fa-id-card"></i> Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="/booking/history"><i class="fas fa-history"></i> Lịch sử đặt tour</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Chưa đăng nhập -->
                        <li class="nav-item"><a class="nav-link" href="/auth/login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link" href="/auth/register"><i class="fas fa-user-plus"></i> Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Banner -->
    <div class="hero-section">
        <div class="container">
            <h1><i class="fas fa-plane"></i> Khám phá Việt Nam cùng BookingTour</h1>
            <p class="lead">Hành trình trải nghiệm - Giá tốt nhất</p>
        </div>
    </div>
    
    <main class="container my-4">