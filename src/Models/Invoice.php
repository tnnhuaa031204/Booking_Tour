<?php
// src/Models/Invoice.php

class Invoice {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy danh sách tất cả hóa đơn
    public function getAll(): array {
        $sql = "SELECT i.*, b.BookingCode, c.FullName as CustomerName
                FROM Invoices i
                JOIN Bookings b ON i.BookingID = b.BookingID
                JOIN Customers c ON b.CustomerID = c.CustomerID
                ORDER BY i.CreatedAt DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy chi tiết hóa đơn theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT i.*, b.BookingCode, b.TotalAmount, b.BookingDate,
                       c.FullName as CustomerName, c.Email as CustomerEmail, c.Phone as CustomerPhone,
                       t.TourName, ts.StartDate, ts.EndDate
                FROM Invoices i
                JOIN Bookings b ON i.BookingID = b.BookingID
                JOIN Customers c ON b.CustomerID = c.CustomerID
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                WHERE i.InvoiceID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Tạo hóa đơn mới từ booking
    public function create(string $invoiceNumber, int $bookingId): bool {
        $sql = "INSERT INTO Invoices (InvoiceNumber, BookingID, InvoiceDate, TotalAmount, IsSentEmail, CreatedAt)
                VALUES (:number, :bookingId, GETDATE(), 
                        (SELECT TotalAmount FROM Bookings WHERE BookingID = :bookingId), 0, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':number' => $invoiceNumber,
            ':bookingId' => $bookingId
        ]);
    }
    
    // Đánh dấu đã gửi email
    public function markAsSent(int $id): bool {
        $sql = "UPDATE Invoices SET IsSentEmail = 1 WHERE InvoiceID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>