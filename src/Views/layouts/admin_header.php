<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Booking Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            overflow-x: hidden;
        }
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h3 {
            margin-bottom: 0;
            font-size: 1.3rem;
        }
        .sidebar-header small {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        .sidebar .nav-link {
            color: #ccc;
            padding: 12px 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link:hover {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background: white;
            padding: 12px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .page-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-name {
            font-weight: 500;
            color: #333;
        }
        .btn-logout {
            background: #dc3545;
            color: white;
            padding: 6px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
        .content {
            padding: 25px;
            background: #f4f6f9;
            flex: 1;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .sidebar-header h3, .sidebar-header small {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-umbrella-beach"></i> BookingTour</h3>
            <small>Quản trị hệ thống</small>
        </div>
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="/admin/dashboard">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>

            <!-- Quản lý Tour -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/tours') !== false ? 'active' : '' ?>" href="/admin/tours">
                    <i class="fas fa-umbrella-beach"></i> <span>Quản lý Tour</span>
                </a>
            </li>

            <!-- Lịch khởi hành -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/schedules') !== false ? 'active' : '' ?>" href="/admin/schedules">
                    <i class="fas fa-calendar-alt"></i> <span>Lịch khởi hành</span>
                </a>
            </li>

            <!-- Quản lý Booking -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/bookings') !== false ? 'active' : '' ?>" href="/admin/bookings">
                    <i class="fas fa-ticket-alt"></i> <span>Quản lý Booking</span>
                </a>
            </li>

            <!-- Quản lý Voucher -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/vouchers') !== false ? 'active' : '' ?>" href="/admin/vouchers">
                    <i class="fas fa-tag"></i> <span>Quản lý Voucher</span>
                </a>
            </li>

            <!-- ====== MENU MỚI ====== -->

            <!-- Quản lý Hóa đơn (MỚI) -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/invoices') !== false ? 'active' : '' ?>" href="/admin/invoices">
                    <i class="fas fa-file-invoice-dollar"></i> <span>Quản lý Hóa đơn</span>
                </a>
            </li>

            <!-- Quản lý Đánh giá (MỚI) -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/reviews') !== false ? 'active' : '' ?>" href="/admin/reviews">
                    <i class="fas fa-star"></i> <span>Quản lý Đánh giá</span>
                </a>
            </li>

            <!-- CRM & Tasks (MỚI) -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false ? 'active' : '' ?>" href="/admin/crm">
                    <i class="fas fa-users-cog"></i> <span>CRM & Tasks</span>
                </a>
            </li>

            <!-- ====== HẾT MENU MỚI ====== -->

            <!-- Quản lý Người dùng -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>" href="/admin/users">
                    <i class="fas fa-users"></i> <span>Quản lý Người dùng</span>
                </a>
            </li>

            <!-- Báo cáo -->
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false ? 'active' : '' ?>" href="/admin/reports">
                    <i class="fas fa-chart-bar"></i> <span>Báo cáo</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="page-title">
                <i class="fas fa-<?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'tachometer-alt' : 'umbrella-beach' ?>"></i>
                <?php
                if (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) echo 'Dashboard';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/tours') !== false) echo 'Quản lý Tour';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/schedules') !== false) echo 'Lịch khởi hành';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/bookings') !== false) echo 'Quản lý Booking';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/vouchers') !== false) echo 'Quản lý Voucher';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) echo 'Quản lý Người dùng';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false) echo 'Báo cáo';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/invoices') !== false) echo 'Quản lý Hóa đơn';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/reviews') !== false) echo 'Quản lý Đánh giá';
                elseif (strpos($_SERVER['REQUEST_URI'], '/admin/crm') !== false) echo 'CRM & Tasks';
                else echo 'Quản trị';
                ?>
            </div>
            <div class="user-info">
                <span class="user-name"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user']['FullName'] ?? 'Admin') ?></span>
                <a href="/auth/logout" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>

        <!-- Content Body -->
        <div class="content">