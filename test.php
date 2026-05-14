<?php
// test.php - Kiểm tra kết nối CSDL

require_once 'config.php';

echo "<h1>🔌 Kiểm tra kết nối BookingTourDB</h1>";

try {
    // Kiểm tra bảng Users
    $stmt = db()->query("SELECT COUNT(*) as total FROM Users");
    $userCount = $stmt->fetch();
    
    // Kiểm tra bảng Tours
    $stmt = db()->query("SELECT COUNT(*) as total FROM Tours");
    $tourCount = $stmt->fetch();
    
    echo "<p style='color:green'>✅ Kết nối thành công!</p>";
    echo "<ul>";
    echo "<li>📊 Số người dùng (Users): <strong>" . $userCount['total'] . "</strong></li>";
    echo "<li>📊 Số tour (Tours): <strong>" . $tourCount['total'] . "</strong></li>";
    echo "</ul>";
    
    // Liệt kê 5 tour đầu tiên
    $stmt = db()->query("SELECT TOP 5 TourID, TourCode, TourName, Duration FROM Tours WHERE IsActive = 1");
    $tours = $stmt->fetchAll();
    
    if (count($tours) > 0) {
        echo "<h3>📋 Danh sách tour (5 tour đầu):</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Mã tour</th><th>Tên tour</th><th>Số ngày</th></tr>";
        foreach ($tours as $tour) {
            echo "<tr>";
            echo "<td>{$tour['TourID']}</td>";
            echo "<td>{$tour['TourCode']}</td>";
            echo "<td>{$tour['TourName']}</td>";
            echo "<td>{$tour['Duration']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ Chưa có dữ liệu tour. Hãy thêm tour vào database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Lỗi truy vấn: " . $e->getMessage() . "</p>";
}
?>