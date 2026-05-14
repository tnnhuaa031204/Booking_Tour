<?php
// src/Models/Task.php

class Task {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy danh sách tất cả tasks
    public function getAll(): array {
        $sql = "SELECT t.*, c.FullName as CustomerName, u.FullName as EmployeeName
                FROM Tasks t
                JOIN Customers c ON t.CustomerID = c.CustomerID
                JOIN Users u ON t.EmployeeID = u.UserID
                ORDER BY t.DueDate ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Tạo task mới
    public function create(int $employeeId, ?int $customerId, string $title, string $dueDate): bool {
        $sql = "INSERT INTO Tasks (EmployeeID, CustomerID, Title, DueDate, IsCompleted, CreatedAt)
                VALUES (:employeeId, :customerId, :title, :dueDate, 0, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':employeeId' => $employeeId,
            ':customerId' => $customerId,
            ':title' => $title,
            ':dueDate' => $dueDate
        ]);
    }
    
    // Đánh dấu hoàn thành
    public function markCompleted(int $id): bool {
        $sql = "UPDATE Tasks SET IsCompleted = 1 WHERE TaskID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>