<?php
// src/Controllers/BaseController.php

class BaseController {
    
    protected function view(string $viewName, array $data = []): void {
        extract($data);
        
        $role = isset($_SESSION['user']['RoleName']) ? strtolower($_SESSION['user']['RoleName']) : 'guest';
        
        // ====== XỬ LÝ HEADER ======
        if ($role === 'guest' || $role === 'customer') {
            $headerFile = __DIR__ . '/../Views/layouts/header.php';
        } else {
            $headerFile = __DIR__ . '/../Views/layouts/admin_header.php';
        }
        require_once $headerFile;
        
        // ====== LOAD VIEW ======
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewName) . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View không tồn tại: $viewName");
        }
        
        // ====== XỬ LÝ FOOTER ======
        if ($role === 'guest' || $role === 'customer') {
            $footerFile = __DIR__ . '/../Views/layouts/footer.php';
        } else {
            $footerFile = __DIR__ . '/../Views/layouts/admin_footer.php';
        }
        require_once $footerFile;
    }
    
    protected function redirect(string $url): void {
        header("Location: $url");
        exit();
    }
    
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    protected function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    protected function hasPermission($permissionCode, $action = 'CanView') {
        if (!isset($_SESSION['user'])) return false;
        
        $userId = $_SESSION['user']['UserID'];
        global $conn;
        $db = $conn;
        
        try {
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
        } catch (Exception $e) {
            return false;
        }
    }
    
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user']);
    }
    
    protected function hasRole($roles): bool {
        if (!isset($_SESSION['user'])) return false;
        
        $userRole = $_SESSION['user']['RoleName'] ?? '';
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }
}
?>