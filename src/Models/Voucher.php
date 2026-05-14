<?php
// src/Models/Voucher.php

class Voucher {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy tất cả voucher
    public function getAll(): array {
        $sql = "SELECT * FROM Vouchers ORDER BY VoucherID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy voucher theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM Vouchers WHERE VoucherID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Lấy voucher theo code (kiểm tra khi áp dụng)
    public function getByCode(string $code): ?array {
        $sql = "SELECT * FROM Vouchers WHERE VoucherCode = :code AND IsActive = 1 
                AND StartDate <= GETDATE() AND EndDate >= GETDATE()
                AND Quantity > UsedCount";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Thêm voucher mới (chỉ giảm số tiền cố định)
    public function create(string $code, string $name, float $discountValue, 
                          float $minOrderValue, string $startDate, string $endDate, int $quantity): bool {
        $sql = "INSERT INTO Vouchers (VoucherCode, VoucherName, DiscountType, DiscountValue, 
                MinOrderValue, MaxDiscount, StartDate, EndDate, Quantity, UsedCount, IsActive, CreatedAt) 
                VALUES (:code, :name, 'Fixed', :value, :minOrder, 0, :startDate, :endDate, :qty, 0, 1, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':code' => $code,
            ':name' => $name,
            ':value' => $discountValue,
            ':minOrder' => $minOrderValue,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':qty' => $quantity
        ]);
    }
    
    // Cập nhật voucher
    public function update(int $id, string $code, string $name, float $discountValue,
                          float $minOrderValue, string $startDate, string $endDate, int $quantity, int $isActive): bool {
        $sql = "UPDATE Vouchers SET VoucherCode = :code, VoucherName = :name, DiscountType = 'Fixed',
                DiscountValue = :value, MinOrderValue = :minOrder, MaxDiscount = 0,
                StartDate = :startDate, EndDate = :endDate, Quantity = :qty, IsActive = :isActive
                WHERE VoucherID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':code' => $code,
            ':name' => $name,
            ':value' => $discountValue,
            ':minOrder' => $minOrderValue,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':qty' => $quantity,
            ':isActive' => $isActive
        ]);
    }
    
    // Xóa voucher
    public function delete(int $id): bool {
        $sql = "DELETE FROM Vouchers WHERE VoucherID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Tăng số lần sử dụng
    public function incrementUsed(int $id): bool {
        $sql = "UPDATE Vouchers SET UsedCount = UsedCount + 1 WHERE VoucherID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Áp dụng voucher, tính số tiền giảm
    public function applyDiscount(string $code, float $totalAmount): array {
        $voucher = $this->getByCode($code);
        
        if (!$voucher) {
            return ['success' => false, 'message' => 'Mã voucher không hợp lệ hoặc đã hết hạn'];
        }
        
        if ($totalAmount < $voucher['MinOrderValue']) {
            return ['success' => false, 'message' => 'Đơn hàng tối thiểu ' . number_format($voucher['MinOrderValue'], 0, ',', '.') . 'đ để áp dụng voucher'];
        }
        
        $discountAmount = $voucher['DiscountValue'];
        if ($discountAmount > $totalAmount) {
            $discountAmount = $totalAmount;
        }
        
        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'voucher_id' => $voucher['VoucherID'],
            'voucher_code' => $voucher['VoucherCode'],
            'message' => 'Áp dụng thành công! Giảm ' . number_format($discountAmount, 0, ',', '.') . 'đ'
        ];
    }
}
?>