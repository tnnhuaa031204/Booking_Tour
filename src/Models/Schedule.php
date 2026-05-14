<?php
// src/Models/Schedule.php

class Schedule {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy tất cả lịch khởi hành (kèm tên tour)
    public function getAll(): array {
        $sql = "SELECT s.*, t.TourName, t.TourCode 
                FROM TourSchedules s
                JOIN Tours t ON s.TourID = t.TourID
                ORDER BY s.StartDate DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy lịch khởi hành theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM TourSchedules WHERE ScheduleID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Lấy danh sách tour để hiển thị trong dropdown
    public function getTours(): array {
        $sql = "SELECT TourID, TourName FROM Tours WHERE IsActive = 1 ORDER BY TourName";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Thêm lịch khởi hành mới
    public function create(int $tourId, string $startDate, string $endDate, float $price, int $totalSlots): bool {
        $sql = "INSERT INTO TourSchedules (TourID, StartDate, EndDate, Price, AvailableSlots, TotalSlots, Status, CreatedAt) 
                VALUES (:tourId, :startDate, :endDate, :price, :totalSlots, :totalSlots, N'Đang mở', GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tourId' => $tourId,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':price' => $price,
            ':totalSlots' => $totalSlots
        ]);
    }
    
    // Cập nhật lịch khởi hành
    public function update(int $id, int $tourId, string $startDate, string $endDate, float $price, int $totalSlots, string $status): bool {
        $sql = "UPDATE TourSchedules 
                SET TourID = :tourId, StartDate = :startDate, EndDate = :endDate, 
                    Price = :price, TotalSlots = :totalSlots, Status = :status
                WHERE ScheduleID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':tourId' => $tourId,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':price' => $price,
            ':totalSlots' => $totalSlots,
            ':status' => $status
        ]);
    }
    
    // Xóa lịch khởi hành
    public function delete(int $id): bool {
        $sql = "DELETE FROM TourSchedules WHERE ScheduleID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>