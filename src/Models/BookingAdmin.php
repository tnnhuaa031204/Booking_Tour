<?php
// src/Models/BookingAdmin.php

class BookingAdmin {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy tất cả booking (kèm thông tin khách hàng, tour)
    public function getAll(): array {
        $sql = "SELECT b.*, c.FullName as CustomerName, c.Email as CustomerEmail, c.Phone as CustomerPhone,
                       t.TourName, t.TourCode, ts.StartDate, ts.EndDate, ts.Price
                FROM Bookings b
                JOIN Customers c ON b.CustomerID = c.CustomerID
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                ORDER BY b.BookingID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy booking theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT b.*, c.FullName as CustomerName, c.Email as CustomerEmail, c.Phone as CustomerPhone,
                       t.TourName, t.TourCode, ts.StartDate, ts.EndDate, ts.Price
                FROM Bookings b
                JOIN Customers c ON b.CustomerID = c.CustomerID
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                WHERE b.BookingID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Cập nhật trạng thái booking
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE Bookings SET BookingStatus = :status, UpdatedAt = GETDATE() WHERE BookingID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
    
    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus(int $id, string $paymentStatus): bool {
        $sql = "UPDATE Bookings SET PaymentStatus = :paymentStatus, UpdatedAt = GETDATE() WHERE BookingID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':paymentStatus' => $paymentStatus, ':id' => $id]);
    }
}
?>