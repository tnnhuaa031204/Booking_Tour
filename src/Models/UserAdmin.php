<?php
// src/Models/UserAdmin.php

class UserAdmin {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy tất cả người dùng
    public function getAll(): array {
        $sql = "SELECT u.*, r.RoleName, c.CustomerID, c.CustomerCode 
                FROM Users u
                JOIN Roles r ON u.RoleID = r.RoleID
                LEFT JOIN Customers c ON u.UserID = c.UserID
                ORDER BY u.UserID DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // Lấy người dùng theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT u.*, r.RoleName 
                FROM Users u
                JOIN Roles r ON u.RoleID = r.RoleID
                WHERE u.UserID = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Khóa/mở tài khoản
    public function toggleActive(int $id, int $isActive): bool {
        $sql = "UPDATE Users SET IsActive = :isActive WHERE UserID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':isActive' => $isActive, ':id' => $id]);
    }
    
    // Xóa người dùng (chỉ customer)
    public function delete(int $id): bool {
        // Xóa customer trước
        $stmt = $this->db->prepare("DELETE FROM Customers WHERE UserID = :id");
        $stmt->execute([':id' => $id]);
        
        $sql = "DELETE FROM Users WHERE UserID = :id AND RoleID = 5";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>