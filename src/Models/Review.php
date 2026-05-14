<?php
// src/Models/Review.php

class Review {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy danh sách tất cả đánh giá
    public function getAll(): array {
        $sql = "SELECT r.*, c.FullName as CustomerName, t.TourName,
                       (SELECT COUNT(*) FROM ReviewReplies WHERE ReviewID = r.ReviewID) as ReplyCount
                FROM Reviews r
                JOIN Customers c ON r.CustomerID = c.CustomerID
                JOIN Tours t ON r.TourID = t.TourID
                ORDER BY r.CreatedAt DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy đánh giá theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT r.*, c.FullName as CustomerName, t.TourName
                FROM Reviews r
                JOIN Customers c ON r.CustomerID = c.CustomerID
                JOIN Tours t ON r.TourID = t.TourID
                WHERE r.ReviewID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Cập nhật trạng thái hiển thị
    public function updateVisibility(int $id, int $isVisible): bool {
        $sql = "UPDATE Reviews SET IsVisible = :visible WHERE ReviewID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':visible' => $isVisible, ':id' => $id]);
    }
    
    // Xóa đánh giá
    public function delete(int $id): bool {
        $sql = "DELETE FROM Reviews WHERE ReviewID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>