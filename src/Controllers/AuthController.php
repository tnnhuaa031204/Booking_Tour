<?php
// src/Controllers/AuthController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController extends BaseController {
    
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Hiển thị form đăng nhập
    public function login() {
        // Nếu đã đăng nhập rồi thì chuyển về trang chủ
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
            return;
        }
        $this->view('auth.login');
    }
    
    // Xử lý đăng nhập
    public function postLogin() {
        if (!$this->isPost()) {
            $this->redirect('/auth/login');
            return;
        }
        
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/auth/login');
            return;
        }
        
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            $_SESSION['user'] = $user;
            unset($_SESSION['error']);
            
            // Chuyển hướng theo vai trò
            switch ($user['RoleName']) {
                case 'Admin':
                    $this->redirect('/admin/dashboard');
                    break;
                case 'Manager':
                    $this->redirect('/manager/dashboard');
                    break;
                case 'Sale':
                    $this->redirect('/sale/dashboard');
                    break;
                case 'Accountant':
                    $this->redirect('/accountant/dashboard');
                    break;
                default:
                    $this->redirect('/');
            }
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không chính xác';
            $this->redirect('/auth/login');
        }
    }
    
    // Hiển thị form đăng ký
    public function register() {
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
            return;
        }
        $this->view('auth.register');
    }
    
    // Xử lý đăng ký
    public function postRegister() {
        if (!$this->isPost()) {
            $this->redirect('/auth/register');
            return;
        }
        
        $username = $this->post('username');
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $fullname = $this->post('fullname');
        $email = $this->post('email');
        $phone = $this->post('phone');
        
        // Kiểm tra dữ liệu
        $errors = [];
        
        if (empty($username)) $errors[] = 'Tên đăng nhập không được để trống';
        if (empty($password)) $errors[] = 'Mật khẩu không được để trống';
        if ($password !== $confirmPassword) $errors[] = 'Mật khẩu xác nhận không khớp';
        if (empty($fullname)) $errors[] = 'Họ tên không được để trống';
        if (empty($email)) $errors[] = 'Email không được để trống';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không đúng định dạng';
        if (strlen($password) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/auth/register');
            return;
        }
        
        // Kiểm tra tồn tại
        if ($this->userModel->checkExists($username, $email)) {
            $_SESSION['error'] = 'Tên đăng nhập hoặc email đã tồn tại';
            $this->redirect('/auth/register');
            return;
        }
        
        // Đăng ký
        if ($this->userModel->register($username, $password, $fullname, $email, $phone)) {
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            $this->redirect('/auth/login');
        } else {
            $_SESSION['error'] = 'Đăng ký thất bại, vui lòng thử lại sau';
            $this->redirect('/auth/register');
        }
    }
    
    // Đăng xuất
    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
}
?>