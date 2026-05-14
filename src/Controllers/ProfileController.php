<?php
// src/Controllers/ProfileController.php

require_once 'BaseController.php';

class ProfileController extends BaseController {
    
    public function __construct() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem thông tin cá nhân';
            $this->redirect('/auth/login');
            exit();
        }
    }
    
    // Hiển thị thông tin cá nhân
    public function index() {
        $userId = $_SESSION['user']['UserID'];
        
        $db = db();
        
        // Lấy thông tin user
        $stmt = $db->prepare("SELECT u.*, r.RoleName 
                              FROM Users u
                              JOIN Roles r ON u.RoleID = r.RoleID
                              WHERE u.UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $user = $stmt->fetch();
        
        // Lấy thông tin customer (nếu có)
        $stmt = $db->prepare("SELECT * FROM Customers WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $customer = $stmt->fetch();
        
        $this->view('profile.index', [
            'user' => $user,
            'customer' => $customer
        ]);
    }
    
    // Cập nhật thông tin cá nhân
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/profile');
            return;
        }
        
        $userId = $_SESSION['user']['UserID'];
        $fullname = $this->post('fullname');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $address = $this->post('address');
        
        $db = db();
        
        // Cập nhật bảng Users
        $stmt = $db->prepare("UPDATE Users SET FullName = :fullname, Email = :email, Phone = :phone WHERE UserID = :userId");
        $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':phone' => $phone,
            ':userId' => $userId
        ]);
        
        // Cập nhật bảng Customers (nếu có)
        $stmt = $db->prepare("UPDATE Customers SET FullName = :fullname, Email = :email, Phone = :phone, Address = :address WHERE UserID = :userId");
        $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':userId' => $userId
        ]);
        
        // Cập nhật session
        $_SESSION['user']['FullName'] = $fullname;
        $_SESSION['user']['Email'] = $email;
        
        $_SESSION['success'] = 'Cập nhật thông tin thành công';
        $this->redirect('/profile');
    }
    
    // Đổi mật khẩu
    public function changePassword() {
        if (!$this->isPost()) {
            $this->redirect('/profile');
            return;
        }
        
        $userId = $_SESSION['user']['UserID'];
        $oldPassword = $this->post('old_password');
        $newPassword = $this->post('new_password');
        $confirmPassword = $this->post('confirm_password');
        
        // Kiểm tra mật khẩu cũ
        $db = db();
        $stmt = $db->prepare("SELECT PasswordHash FROM Users WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $user = $stmt->fetch();
        
        if (!password_verify($oldPassword, $user['PasswordHash'])) {
            $_SESSION['error'] = 'Mật khẩu cũ không chính xác';
            $this->redirect('/profile');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu mới không khớp';
            $this->redirect('/profile');
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            $this->redirect('/profile');
            return;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE Users SET PasswordHash = :password WHERE UserID = :userId");
        $stmt->execute([
            ':password' => $hashedPassword,
            ':userId' => $userId
        ]);
        
        $_SESSION['success'] = 'Đổi mật khẩu thành công';
        $this->redirect('/profile');
    }
}
?>