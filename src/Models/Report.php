<?php
// src/Models/Report.php

class Report {
    private \PDO $db;

    public function __construct() {
        $this->db = db();
    }

    // Doanh thu theo tháng trong năm
    public function getMonthlyRevenue(int $year): array {
        $sql = "SELECT 
                    MONTH(BookingDate) as month,
                    SUM(TotalAmount) as revenue
                FROM Bookings
                WHERE YEAR(BookingDate) = :year
                  AND PaymentStatus = :status
                GROUP BY MONTH(BookingDate)
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year, ':status' => 'Đã thanh toán']);
        $results = $stmt->fetchAll();

        $monthly = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthly[$row['month']] = (float)$row['revenue'];
        }
        return $monthly;
    }

    // Doanh thu theo năm
    public function getYearlyRevenue(): array {
        $sql = "SELECT 
                    YEAR(BookingDate) as year,
                    SUM(TotalAmount) as revenue
                FROM Bookings
                WHERE PaymentStatus = :status
                GROUP BY YEAR(BookingDate)
                ORDER BY year DESC
                OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => 'Đã thanh toán']);
        $results = $stmt->fetchAll();

        $yearly = [];
        foreach ($results as $row) {
            $yearly[$row['year']] = (float)$row['revenue'];
        }
        return $yearly;
    }

    // Top tour bán chạy
    public function getTopTours(int $limit = 10): array {
        $sql = "SELECT TOP " . $limit . "
                    t.TourID, t.TourCode, t.TourName,
                    COUNT(b.BookingID) as total_bookings,
                    SUM(b.TotalAmount) as total_revenue
                FROM Tours t
                JOIN TourSchedules ts ON t.TourID = ts.TourID
                JOIN Bookings b ON ts.ScheduleID = b.ScheduleID
                WHERE b.BookingStatus != :cancelled
                GROUP BY t.TourID, t.TourCode, t.TourName
                ORDER BY total_bookings DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cancelled' => 'Đã hủy']);
        return $stmt->fetchAll();
    }

    // Thống kê tổng quan
    public function getSummary(): array {
        $stmt = $this->db->prepare("SELECT ISNULL(SUM(TotalAmount), 0) as total FROM Bookings WHERE PaymentStatus = :status");
        $stmt->execute([':status' => 'Đã thanh toán']);
        $totalRevenue = $stmt->fetch()['total'] ?? 0;

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM Bookings");
        $totalBookings = $stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM Customers");
        $totalCustomers = $stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM Tours WHERE IsActive = 1");
        $totalTours = $stmt->fetch()['total'];

        return [
            'total_revenue'   => $totalRevenue,
            'total_bookings'  => $totalBookings,
            'total_customers' => $totalCustomers,
            'total_tours'     => $totalTours,
        ];
    }
}
?>