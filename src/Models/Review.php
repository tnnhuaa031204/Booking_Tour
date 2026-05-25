<?php
// src/Models/Review.php

class Review {
    private \PDO $db;

    public function __construct() {
        $this->db = db();
    }

    // Lấy tất cả đánh giá (admin)
    public function getAll(): array {
        $sql = "SELECT r.*, u.FullName as CustomerName, t.TourName,
                       (SELECT COUNT(*) FROM ReviewReplies WHERE ReviewID = r.ReviewID) as ReplyCount
                FROM Reviews r
                JOIN Customers c ON r.CustomerID = c.CustomerID
                JOIN Users u ON c.UserID = u.UserID
                JOIN Tours t ON r.TourID = t.TourID
                ORDER BY r.CreatedAt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Lấy đánh giá theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT r.*, u.FullName as CustomerName, t.TourName
                FROM Reviews r
                JOIN Customers c ON r.CustomerID = c.CustomerID
                JOIN Users u ON c.UserID = u.UserID
                JOIN Tours t ON r.TourID = t.TourID
                WHERE r.ReviewID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Lấy đánh giá của 1 user (customer xem lại)
    public function getByUserId(int $userId): array {
        $sql = "SELECT r.*, t.TourName, t.TourCode,
                       b.BookingCode, b.BookingStatus
                FROM Reviews r
                JOIN Customers c ON r.CustomerID = c.CustomerID
                JOIN Tours t ON r.TourID = t.TourID
                JOIN Bookings b ON r.BookingID = b.BookingID
                WHERE c.UserID = :userId
                ORDER BY r.CreatedAt DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    // Lấy booking để review (kiểm tra quyền sở hữu)
    public function getBookingForReview(int $bookingId, int $userId): ?array {
        $sql = "SELECT b.BookingID, b.BookingCode, b.BookingStatus,
                       b.CustomerID, b.TotalAmount,
                       ts.TourID, t.TourName, t.TourCode,
                       ts.StartDate, ts.EndDate
                FROM Bookings b
                JOIN TourSchedules ts ON b.ScheduleID = ts.ScheduleID
                JOIN Tours t ON ts.TourID = t.TourID
                JOIN Customers c ON b.CustomerID = c.CustomerID
                WHERE b.BookingID = :bookingId
                  AND c.UserID = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':bookingId' => $bookingId, ':userId' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Kiểm tra đã đánh giá booking này chưa
    public function hasReviewed(int $bookingId): bool {
        $sql  = "SELECT COUNT(*) as cnt FROM Reviews WHERE BookingID = :bookingId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':bookingId' => $bookingId]);
        return (int)$stmt->fetch()['cnt'] > 0;
    }

    // Tạo đánh giá mới
    public function create(int $bookingId, int $customerId, int $tourId, int $rating, string $comment): bool {
        $sql  = "INSERT INTO Reviews (BookingID, CustomerID, TourID, Rating, Comment, IsVisible, CreatedAt)
                 VALUES (:bookingId, :customerId, :tourId, :rating, :comment, 0, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':bookingId'  => $bookingId,
            ':customerId' => $customerId,
            ':tourId'     => $tourId,
            ':rating'     => $rating,
            ':comment'    => $comment,
        ]);
    }

    // Cập nhật hiển thị (admin)
    public function updateVisibility(int $id, int $isVisible): bool {
        $stmt = $this->db->prepare("UPDATE Reviews SET IsVisible = :visible WHERE ReviewID = :id");
        return $stmt->execute([':visible' => $isVisible, ':id' => $id]);
    }

    // Xóa đánh giá (admin)
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Reviews WHERE ReviewID = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>