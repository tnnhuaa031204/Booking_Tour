<?php
// src/Models/CRMLog.php

class CRMLog {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy danh sách tất cả logs
    public function getAll(): array {
        $sql = "SELECT l.*, c.FullName as CustomerName, u.FullName as EmployeeName
                FROM CRMLogs l
                JOIN Customers c ON l.CustomerID = c.CustomerID
                JOIN Users u ON l.EmployeeID = u.UserID
                ORDER BY l.CreatedAt DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Tạo log mới
    public function create(int $customerId, int $employeeId, string $interactionType, string $content): bool {
        $sql = "INSERT INTO CRMLogs (CustomerID, EmployeeID, InteractionType, Content, CreatedAt)
                VALUES (:customerId, :employeeId, :interactionType, :content, GETDATE())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':customerId' => $customerId,
            ':employeeId' => $employeeId,
            ':interactionType' => $interactionType,
            ':content' => $content
        ]);
    }
}
?>