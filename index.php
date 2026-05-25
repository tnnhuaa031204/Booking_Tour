<?php

// ====== SERVE FILE TĨNH (ảnh, css, js) ======
$staticPath = __DIR__ . '/public' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($staticPath)) {
    $ext = strtolower(pathinfo($staticPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($staticPath);
        exit();
    }
}
// =============================================

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// ========== HELPER: chuyển kebab-case sang camelCase ==========
function toCamelCase($str) {
    $parts = explode('-', $str);
    $first = array_shift($parts);
    return $first . implode('', array_map('ucfirst', $parts));
}
// ==============================================================

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

// ====== XỬ LÝ ROUTE ADMIN ======
if ($parts[0] == 'admin') {
    $controllerName = 'AdminController';

    // ====== BÁO CÁO THỐNG KÊ ======
    if (isset($parts[1]) && $parts[1] == 'reports') {
        if (isset($parts[2])) {
            $actionName = 'report' . ucfirst(toCamelCase($parts[2]));
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'reports';
            $id = null;
        }
    }
    // ====== QUẢN LÝ VOUCHER ======
    elseif (isset($parts[1]) && $parts[1] == 'voucher') {
        if (isset($parts[2])) {
            $actionName = 'voucher' . ucfirst(toCamelCase($parts[2]));
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'vouchers';
            $id = null;
        }
    }
    // ====== QUẢN LÝ LỊCH KHỞI HÀNH ======
    elseif (isset($parts[1]) && $parts[1] == 'schedule') {
        if (isset($parts[2])) {
            $actionName = 'schedule' . ucfirst(toCamelCase($parts[2]));
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'schedules';
            $id = null;
        }
    }
    // ====== QUẢN LÝ BOOKING ======
    elseif (isset($parts[1]) && $parts[1] == 'bookings') {
        if (isset($parts[2])) {
            $actionName = 'booking' . ucfirst(toCamelCase($parts[2]));
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'bookings';
            $id = null;
        }
    }
    // ====== QUẢN LÝ HÓA ĐƠN ======
    elseif (isset($parts[1]) && $parts[1] == 'invoices') {
        $controllerName = 'InvoiceController';
        if (isset($parts[2])) {
            $actionName = toCamelCase($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== QUẢN LÝ ĐÁNH GIÁ ======
    elseif (isset($parts[1]) && $parts[1] == 'reviews') {
        $controllerName = 'ReviewController';
        if (isset($parts[2])) {
            $actionName = toCamelCase($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== CRM ======
    elseif (isset($parts[1]) && $parts[1] == 'crm') {
        $controllerName = 'CRMController';
        if (isset($parts[2])) {
            $actionName = toCamelCase($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'index';
            $id = null;
        }
    }
    // ====== QUẢN LÝ NGƯỜI DÙNG ======
    elseif (isset($parts[1]) && $parts[1] == 'users') {
        if (isset($parts[2])) {
            $actionName = 'user' . ucfirst(toCamelCase($parts[2]));
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'users';
            $id = null;
        }
    }
    // ====== QUẢN LÝ TOUR ======
    elseif (isset($parts[1]) && $parts[1] == 'tours') {
        if (isset($parts[2])) {
            $actionName = toCamelCase($parts[2]);
            $id = $parts[3] ?? null;
        } else {
            $actionName = 'tours';
            $id = null;
        }
    }
    else {
        $actionName = isset($parts[1]) ? toCamelCase($parts[1]) : 'dashboard';
        $id = isset($parts[2]) ? $parts[2] : null;
    }
}
// ====== XỬ LÝ ROUTE THANH TOÁN ======
elseif ($parts[0] == 'payment') {
    $controllerName = 'PaymentController';
    $actionName = isset($parts[1]) ? toCamelCase($parts[1]) : 'index';
    $id = isset($parts[2]) ? $parts[2] : null;
}
// ====== XỬ LÝ CÁC ROUTE CÔNG KHAI ======
elseif ($parts[0] == 'review') {
    $controllerName = 'ReviewController';
    $actionName = isset($parts[1]) ? toCamelCase($parts[1]) : 'myReviews';
    $id = isset($parts[2]) ? $parts[2] : null;
}
elseif ($parts[0] == 'home' || $parts[0] == 'auth' || $parts[0] == 'booking' || $parts[0] == 'tour' || $parts[0] == 'profile') {
    $controllerName = ucfirst($parts[0]) . 'Controller';
    $actionName = isset($parts[1]) ? toCamelCase($parts[1]) : 'index';
    $id = isset($parts[2]) ? $parts[2] : null;
}
else {
    header('Location: /home/index');
    exit();
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