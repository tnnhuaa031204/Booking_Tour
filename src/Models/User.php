<?php
// src/Models/User.php

class User {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Đăng ký tài khoản mới
    public function register(string $username, string $password, string $fullname, string $email, string $phone): bool {
        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Bắt đầu transaction
        $this->db->beginTransaction();
        
        try {
            // 1. Thêm vào bảng Users (role_id = 5 là Customer)
            $sql = "INSERT INTO Users (Username, PasswordHash, FullName, Email, Phone, RoleID, IsActive, CreatedAt) 
                    VALUES (:username, :password, :fullname, :email, :phone, 5, 1, GETDATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':fullname' => $fullname,
                ':email' => $email,
                ':phone' => $phone
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // 2. Thêm vào bảng Customers
            $customerCode = 'KH' . str_pad($userId, 6, '0', STR_PAD_LEFT);
            $sql = "INSERT INTO Customers (UserID, CustomerCode, FullName, Email, Phone, CreatedAt) 
                    VALUES (:userid, :customercode, :fullname, :email, :phone, GETDATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':userid' => $userId,
                ':customercode' => $customerCode,
                ':fullname' => $fullname,
                ':email' => $email,
                ':phone' => $phone
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Kiểm tra đăng nhập
    public function login(string $username, string $password): ?array {
        // SQL Server: không được dùng cùng một tham số 2 lần
        $sql = "SELECT u.*, r.RoleName 
                FROM Users u
                JOIN Roles r ON u.RoleID = r.RoleID
                WHERE (u.Username = :user1 OR u.Email = :user2) AND u.IsActive = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user1' => $username,
            ':user2' => $username
        ]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['PasswordHash'])) {
            // Xóa mật khẩu trước khi lưu session
            unset($user['PasswordHash']);
            return $user;
        }
        
        return null;
    }
    
    // Kiểm tra username hoặc email đã tồn tại chưa
    public function checkExists(string $username, string $email): bool {
        $sql = "SELECT COUNT(*) as total FROM Users WHERE Username = :user OR Email = :mail";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user' => $username,
            ':mail' => $email
        ]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
    
    // Lấy thông tin user theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT u.UserID, u.Username, u.FullName, u.Email, u.Phone, u.IsActive, u.CreatedAt,
                       r.RoleID, r.RoleName,
                       c.CustomerID, c.CustomerCode, c.CustomerType
                FROM Users u
                JOIN Roles r ON u.RoleID = r.RoleID
                LEFT JOIN Customers c ON u.UserID = c.UserID
                WHERE u.UserID = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Cập nhật thông tin user
    public function update(int $id, string $fullname, string $email, string $phone): bool {
        $sql = "UPDATE Users SET FullName = :fullname, Email = :email, Phone = :phone WHERE UserID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':phone' => $phone,
            ':id' => $id
        ]);
    }
    
    // Đổi mật khẩu
    public function changePassword(int $id, string $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET PasswordHash = :password WHERE UserID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }
}
?>