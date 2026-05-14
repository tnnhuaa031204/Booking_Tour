<?php
// config.php - Cấu hình kết nối SQL Server

$server = "TOBIEE04\SQLEXPRESS";
$database = "BookingTourDB";

try {
    // PDO sẽ tự động dùng Windows Authentication
    $dsn = "sqlsrv:Server=$server;Database=$database";
    $conn = new PDO($dsn);
    
    // Thiết lập chế độ lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    
} catch (PDOException $e) {
    die("❌ Lỗi kết nối SQL Server: " . $e->getMessage());
}

function db() {
    global $conn;
    return $conn;
}
// Cấu hình gửi email (dùng Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');      // Thay bằng email của bạn
define('SMTP_PASS', 'your_app_password');          // Thay bằng mật khẩu ứng dụng Gmail
define('SMTP_FROM', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'BookingTour - Hệ thống đặt tour');
?>