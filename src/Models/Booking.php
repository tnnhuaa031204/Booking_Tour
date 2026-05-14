<?php
// src/Models/Booking.php

class Booking {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Tạo booking mới
    public function create(int $customerId, int $scheduleId, int $adultCount, int $childCount, float $totalAmount, ?string $notes = null): int|false {
        $bookingCode = 'BK' . date('YmdHis') . rand(100, 999);
        
        $sql = "INSERT INTO Bookings (BookingCode, CustomerID, ScheduleID, BookingDate, AdultCount, ChildCount, TotalAmount, PaymentStatus, BookingStatus, Notes, CreatedAt) 
                VALUES (:code, :customerId, :scheduleId, GETDATE(), :adultCount, :childCount, :totalAmount, 'Chưa thanh toán', 'Chờ xác nhận', :notes, GETDATE())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':code' => $bookingCode,
            ':customerId' => $customerId,
            ':scheduleId' => $scheduleId,
            ':adultCount' => $adultCount,
            ':childCount' => $childCount,
            ':totalAmount' => $totalAmount,
            ':notes' => $notes
        ]);
        
        return $this->db->lastInsertId();
    }
    
    // Lấy thông tin booking theo ID
    public function getById(int $bookingId): ?array {
        $sql = "SELECT b.*, ts.StartDate, ts.EndDate, ts.Price, t.TourName, t.TourCode, t.Duration
                FROM Bookings b
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                WHERE b.BookingID = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $bookingId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Lấy danh sách booking của khách hàng
    public function getByCustomer(int $customerId): array {
        $sql = "SELECT b.*, ts.StartDate, ts.EndDate, t.TourName, t.TourCode
                FROM Bookings b
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                WHERE b.CustomerID = :customerId
                ORDER BY b.CreatedAt DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':customerId' => $customerId]);
        return $stmt->fetchAll();
    }
    
    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus(int $bookingId, string $status): bool {
        $sql = "UPDATE Bookings SET PaymentStatus = :status, UpdatedAt = GETDATE() WHERE BookingID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $bookingId]);
    }
    
    // Cập nhật trạng thái booking
    public function updateStatus(int $bookingId, string $status): bool {
        $sql = "UPDATE Bookings SET BookingStatus = :status, UpdatedAt = GETDATE() WHERE BookingID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $bookingId]);
    }
    
    // ========== HỦY BOOKING VÀ HOÀN TRẢ VOUCHER ==========
    
    public function cancelBooking(int $bookingId, int $customerId): bool {
        // Bắt đầu transaction
        $this->db->beginTransaction();
        
        try {
            // 1. Kiểm tra booking có thuộc về customer này không và có thể hủy không
            $sql = "SELECT BookingID, BookingStatus, PaymentStatus 
                    FROM Bookings 
                    WHERE BookingID = :id AND CustomerID = :customerId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $bookingId, ':customerId' => $customerId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                $this->db->rollBack();
                return false;
            }
            
            // Chỉ cho phép hủy nếu chưa thanh toán và chưa xác nhận
            if ($booking['PaymentStatus'] != 'Chưa thanh toán' || $booking['BookingStatus'] != 'Chờ xác nhận') {
                $this->db->rollBack();
                return false;
            }
            
            // 2. Lấy thông tin voucher usage của booking này
            $sql = "SELECT VoucherID, UsageID FROM VoucherUsages WHERE BookingID = :bookingId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':bookingId' => $bookingId]);
            $voucherUsage = $stmt->fetch();
            
            // 3. Cập nhật trạng thái booking thành "Đã hủy"
            $sql = "UPDATE Bookings SET BookingStatus = 'Đã hủy', UpdatedAt = GETDATE() WHERE BookingID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $bookingId]);
            
            // 4. Nếu có voucher, hoàn trả số lần sử dụng
            if ($voucherUsage && $voucherUsage['VoucherID']) {
                // Giảm UsedCount trong bảng Vouchers
                $sql = "UPDATE Vouchers SET UsedCount = UsedCount - 1 WHERE VoucherID = :voucherId";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':voucherId' => $voucherUsage['VoucherID']]);
                
                // Xóa bản ghi trong VoucherUsages
                $sql = "DELETE FROM VoucherUsages WHERE UsageID = :usageId";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':usageId' => $voucherUsage['UsageID']]);
            }
            
            // Commit transaction
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Cancel booking error: " . $e->getMessage());
            return false;
        }
    }
    
    // ========== CÁC HÀM BỔ SUNG ==========
    
    /**
     * Lấy booking code theo ID
     */
    public function getBookingCode(int $bookingId): ?string {
        $sql = "SELECT BookingCode FROM Bookings WHERE BookingID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $bookingId]);
        $result = $stmt->fetch();
        return $result ? $result['BookingCode'] : null;
    }
    
    /**
     * Lấy thông tin đầy đủ của booking kèm thông tin khách hàng và tour
     * Dùng cho email xác nhận
     */
    public function getBookingDetail(int $bookingId): ?array {
        $sql = "SELECT 
                    b.BookingID, b.BookingCode, b.AdultCount, b.ChildCount, b.TotalAmount, 
                    b.DiscountAmount, b.PaymentStatus, b.BookingStatus, b.Notes, b.BookingDate,
                    c.CustomerID, c.FullName as CustomerName, c.Email as CustomerEmail, c.Phone as CustomerPhone,
                    ts.ScheduleID, ts.StartDate, ts.EndDate, ts.Price,
                    t.TourID, t.TourName, t.TourCode, t.Duration
                FROM Bookings b
                JOIN Customers c ON b.CustomerID = c.CustomerID
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                WHERE b.BookingID = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $bookingId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Lấy danh sách booking theo trạng thái (dùng cho admin)
     */
    public function getByStatus(string $status): array {
        $sql = "SELECT b.*, ts.StartDate, t.TourName, c.FullName as CustomerName
                FROM Bookings b
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                JOIN Customers c ON b.CustomerID = c.CustomerID
                WHERE b.BookingStatus = :status
                ORDER BY b.CreatedAt DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Thống kê số lượng booking theo ngày (dùng cho báo cáo)
     */
    public function countByDate(string $date): int {
        $sql = "SELECT COUNT(*) as count FROM Bookings WHERE CAST(BookingDate AS DATE) = :date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>