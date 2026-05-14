<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// ========== ROUTE ĐẶC BIỆT CHO AJAX APPLY VOUCHER ==========
if (strpos($_SERVER['REQUEST_URI'], '/booking/applyVoucher') !== false) {
    require_once __DIR__ . '/src/Controllers/BookingController.php';
    $controller = new BookingController();
    $controller->applyVoucher();
    exit();
}
// ============================================================

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');
$path = strtok($path, '?');

if (empty($path)) {
    $path = 'home/index';
}

$parts = explode('/', $path);
$controllerName = ucfirst($parts[0]) . 'Controller';

// Xử lý đặc biệt cho admin
if ($parts[0] == 'admin') {
    // ====== BÁO CÁO THỐNG KÊ ======
    if (isset($parts[1]) && $parts[1] == 'reports') {
        if (isset($parts[2])) {
            $actionName = 'report' . ucfirst($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'reports';
            $id = null;
        }
    }
    // ====== QUẢN LÝ VOUCHER ======
    elseif (isset($parts[1]) && $parts[1] == 'voucher') {
        if (isset($parts[2])) {
            $actionName = 'voucher' . ucfirst($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'vouchers';
            $id = null;
        }
    }
    // ====== QUẢN LÝ LỊCH KHỞI HÀNH ======
    elseif (isset($parts[1]) && $parts[1] == 'schedule') {
        if (isset($parts[2])) {
            $actionName = 'schedule' . ucfirst($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'schedules';
            $id = null;
        }
    }
    // ====== QUẢN LÝ BOOKING ======
    elseif (isset($parts[1]) && $parts[1] == 'bookings') {
        if (isset($parts[2])) {
            $actionName = 'booking' . ucfirst($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'bookings';
            $id = null;
        }
    }
    // ====== QUẢN LÝ HÓA ĐƠN (MỚI) ======
    elseif (isset($parts[1]) && $parts[1] == 'invoices') {
        $controllerName = 'InvoiceController';
        if (isset($parts[2])) {
            $actionName = $parts[2];
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== QUẢN LÝ ĐÁNH GIÁ (MỚI) ======
    elseif (isset($parts[1]) && $parts[1] == 'reviews') {
        $controllerName = 'ReviewController';
        if (isset($parts[2])) {
            $actionName = $parts[2];
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== CRM (MỚI) ======
    elseif (isset($parts[1]) && $parts[1] == 'crm') {
        $controllerName = 'CRMController';
        if (isset($parts[2])) {
            $actionName = $parts[2];
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== QUẢN LÝ NGƯỜI DÙNG ======
    elseif (isset($parts[1]) && $parts[1] == 'users') {
        if (isset($parts[2])) {
            $actionName = 'user' . ucfirst($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'users';
            $id = null;
        }
    }
    // ====== QUẢN LÝ TOUR ======
    elseif (isset($parts[1]) && $parts[1] == 'tours') {
        if (isset($parts[2])) {
            $actionName = $parts[2];
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'tours';
            $id = null;
        }
    }
    else {
        $actionName = isset($parts[1]) ? $parts[1] : 'dashboard';
        $id = isset($parts[2]) ? $parts[2] : null;
    }
} else {
    $actionName = isset($parts[1]) ? $parts[1] : 'index';
    $id = isset($parts[2]) ? $parts[2] : null;
}

$controllerFile = __DIR__ . '/src/Controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        if (method_exists($controller, $actionName)) {
            if ($id !== null) {
                $controller->$actionName($id);
            } else {
                $controller->$actionName();
            }
        } else {
            http_response_code(404);
            echo "404 - Không tìm thấy action: $actionName";
        }
    } else {
        http_response_code(404);
        echo "404 - Không tìm thấy controller class: $controllerName";
    }
} else {
    http_response_code(404);
    echo "404 - Không tìm thấy file controller: $controllerFile";
}
?>