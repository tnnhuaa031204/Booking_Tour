<?php
// src/Controllers/ForgotController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Services/EmailService.php';

class ForgotController extends BaseController {
    
    // Hiển thị form quên mật khẩu
    public function index() {
        $this->view('auth.forgot');
    }
    
    // Xử lý yêu cầu quên mật khẩu
    public function request() {
        if (!$this->isPost()) {
            $this->redirect('/forgot');
            return;
        }
        
        $username = $this->post('username');
        $email = $this->post('email');
        
        if (empty($username) || empty($email)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/forgot');
            return;
        }
        
        $db = db();
        
        // Kiểm tra username và email có khớp không
        $stmt = $db->prepare("SELECT UserID, Username, Email, FullName FROM Users WHERE Username = :username AND Email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['error'] = 'Tên đăng nhập và email không khớp';
            $this->redirect('/forgot');
            return;
        }
        
        // Tạo mật khẩu mới ngẫu nhiên
        $newPassword = $this->generateRandomPassword();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu mới vào database
        $stmt = $db->prepare("UPDATE Users SET PasswordHash = :password WHERE UserID = :id");
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $user['UserID']
        ]);
        
        // Gửi email chứa mật khẩu mới
        $emailService = new EmailService();
        $subject = "Khôi phục mật khẩu - BookingTour";
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0066cc; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .new-password { font-size: 20px; font-weight: bold; color: #0066cc; background: #f0f0f0; padding: 10px; text-align: center; }
                .footer { background: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>BookingTour - Khôi phục mật khẩu</h2>
                </div>
                <div class='content'>
                    <p>Chào <strong>{$user['FullName']}</strong>,</p>
                    <p>Chúng tôi đã nhận được yêu cầu khôi phục mật khẩu của bạn.</p>
                    <p>Mật khẩu mới của bạn là:</p>
                    <div class='new-password'>$newPassword</div>
                    <p>Vui lòng đăng nhập lại và đổi mật khẩu để bảo mật.</p>
                    <p><a href='http://localhost:8000/auth/login'>Đăng nhập ngay</a></p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 BookingTour - Hệ thống đặt tour du lịch trực tuyến</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $emailService->send($user['Email'], $subject, $body);
        
        $_SESSION['success'] = 'Mật khẩu mới đã được gửi đến email của bạn';
        $this->redirect('/auth/login');
    }
    
    // Tạo mật khẩu ngẫu nhiên
    private function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
?>