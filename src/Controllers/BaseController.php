<?php
// src/Controllers/BaseController.php

class BaseController {
    
    /**
     * Hiển thị view
     * @param string $viewName Tên view (vd: 'home.index')
     * @param array $data Dữ liệu truyền vào view
     */
    protected function view(string $viewName, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewName) . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View không tồn tại: $viewName");
        }
    }
    
    /**
     * Chuyển hướng đến URL khác
     * @param string $url Đường dẫn chuyển hướng
     */
    protected function redirect(string $url): void {
        header("Location: $url");
        exit();
    }
    
    /**
     * Kiểm tra xem có phải POST request không
     * @return bool
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Lấy giá trị từ POST
     * @param string $key Tên field
     * @param mixed $default Giá trị mặc định
     * @return mixed
     */
    protected function post(string $key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Lấy giá trị từ GET
     * @param string $key Tên field
     * @param mixed $default Giá trị mặc định
     * @return mixed
     */
    protected function get(string $key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    // ========== HÀM MỚI ==========
    
    /**
     * Kiểm tra quyền của người dùng
     * @param string $permissionCode Mã quyền (ví dụ: 'MANAGE_TOURS')
     * @param string $action Hành động (CanView, CanCreate, CanEdit, CanDelete, CanApprove, CanExport)
     * @return bool
     */
    protected function hasPermission($permissionCode, $action = 'CanView') {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        $userId = $_SESSION['user']['UserID'];
        $db = db();
        
        $stmt = $db->prepare("
            SELECT rp.$action
            FROM Users u
            JOIN Roles r ON u.RoleID = r.RoleID
            JOIN RolePermissions rp ON r.RoleID = rp.RoleID
            JOIN Permissions p ON rp.PermissionID = p.PermissionID
            WHERE u.UserID = :userId AND p.PermissionCode = :permissionCode
        ");
        $stmt->execute([
            ':userId' => $userId,
            ':permissionCode' => $permissionCode
        ]);
        $result = $stmt->fetch();
        
        return $result ? (bool)$result[$action] : false;
    }
    
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     * @return bool
     */
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user']);
    }
    
    /**
     * Kiểm tra role của người dùng
     * @param string|array $roles Tên role hoặc mảng các role
     * @return bool
     */
    protected function hasRole($roles): bool {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        $userRole = $_SESSION['user']['RoleName'] ?? '';
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }
    
    /**
     * Lấy thông tin người dùng hiện tại
     * @return array|null
     */
    protected function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Kiểm tra CSRF token (bảo mật form)
     * @return bool
     */
    protected function validateCsrf(): bool {
        $token = $this->post('_token', '');
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Tạo CSRF token
     * @return string
     */
    protected function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>