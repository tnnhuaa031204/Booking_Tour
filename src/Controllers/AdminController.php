<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Tour.php';
require_once __DIR__ . '/../Models/Schedule.php';
require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/../Models/BookingAdmin.php';
require_once __DIR__ . '/../Models/UserAdmin.php';
require_once __DIR__ . '/../Models/Report.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Review.php';        // Thêm
require_once __DIR__ . '/../Models/CRMLog.php';        // Thêm
require_once __DIR__ . '/../Models/Task.php';          // Thêm
require_once __DIR__ . '/../Models/Invoice.php';       // Thêm

class AdminController extends BaseController {
    
    private Tour $tourModel;
    private Booking $bookingModel;
    
    public function __construct() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            $this->redirect('/auth/login');
            exit();
        }
        
        $this->tourModel = new Tour();
        $this->bookingModel = new Booking();
    }
    
    // ==================== DASHBOARD ====================
    
    public function dashboard() {
        $role = $_SESSION['user']['RoleName'] ?? '';
        
        // === DASHBOARD CHO MANAGER ===
        if ($role === 'Manager') {
            $totalRevenue = $this->bookingModel->getTotalRevenue();
            $totalBookings = $this->bookingModel->getTotalBookings();
            $pendingBookings = $this->bookingModel->getPendingBookings();
            $activeTours = $this->tourModel->getActiveToursCount();
            
            $this->view('manager.dashboard', [
                'totalRevenue' => $totalRevenue,
                'totalBookings' => $totalBookings,
                'pendingBookings' => $pendingBookings,
                'activeTours' => $activeTours
            ]);
            return;
        }
        
        // === DASHBOARD CHO SALE ===
        if ($role === 'Sale') {
            $userId = $_SESSION['user']['UserID'];
            
            $db = db();
            
            // Số booking do Sale tạo
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM Bookings WHERE EmployeeID = :userId");
            $stmt->execute([':userId' => $userId]);
            $myBookings = $stmt->fetch()['total'];
            
            // Số khách hàng đang chăm sóc (CRM logs)
            $stmt = $db->prepare("SELECT COUNT(DISTINCT CustomerID) as total FROM CRMLogs WHERE EmployeeID = :userId");
            $stmt->execute([':userId' => $userId]);
            $myCustomers = $stmt->fetch()['total'];
            
            // Tour đang hoạt động
            $activeTours = $this->tourModel->getActiveToursCount();
            
            // Doanh thu từ booking của Sale
            $stmt = $db->prepare("SELECT SUM(TotalAmount) as total FROM Bookings WHERE EmployeeID = :userId AND PaymentStatus = 'Đã thanh toán'");
            $stmt->execute([':userId' => $userId]);
            $myRevenue = $stmt->fetch()['total'] ?? 0;
            
            $this->view('sale.dashboard', [
                'myBookings' => $myBookings,
                'myCustomers' => $myCustomers,
                'activeTours' => $activeTours,
                'myRevenue' => $myRevenue
            ]);
            return;
        }
        
        // === DASHBOARD CHO ACCOUNTANT ===
        if ($role === 'Accountant') {
            $totalRevenue = $this->bookingModel->getTotalRevenue();
            $totalBookings = $this->bookingModel->getTotalBookings();
            
            $db = db();
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM Bookings WHERE PaymentStatus = 'Chưa thanh toán'");
            $stmt->execute();
            $pendingPayments = $stmt->fetch()['total'];
            
            $stmt = $db->prepare("SELECT SUM(TotalAmount) as total FROM Bookings WHERE PaymentStatus = 'Chưa thanh toán'");
            $stmt->execute();
            $debt = $stmt->fetch()['total'] ?? 0;
            
            $this->view('accountant.dashboard', [
                'totalRevenue' => $totalRevenue,
                'totalBookings' => $totalBookings,
                'pendingPayments' => $pendingPayments,
                'debt' => $debt
            ]);
            return;
        }
        
        // === DASHBOARD CHO ADMIN (MẶC ĐỊNH) ===
        $db = db();
        $stmt = $db->query("SELECT COUNT(*) as total FROM Tours WHERE IsActive = 1");
        $totalTours = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM Bookings");
        $totalBookings = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM Customers");
        $totalCustomers = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT SUM(TotalAmount) as total FROM Bookings WHERE PaymentStatus = 'Đã thanh toán'");
        $totalRevenue = $stmt->fetch()['total'] ?? 0;
        
        $this->view('admin.dashboard', [
            'totalTours' => $totalTours,
            'totalBookings' => $totalBookings,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue
        ]);
    }
    
    // ==================== QUẢN LÝ TOUR ====================
    
    public function tours() {
        if (!$this->hasPermission('TOUR_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem tour';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $db = db();
        $stmt = $db->query("SELECT t.*, (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL FROM Tours t ORDER BY t.TourID DESC");
        $tours = $stmt->fetchAll();
        $this->view('admin.tours.index', ['tours' => $tours]);
    }
    
    public function create() {
        if (!$this->hasPermission('TOUR_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm tour';
            $this->redirect('/admin/tours');
            return;
        }
        $this->view('admin.tours.create');
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/tours');
            return;
        }
        
        if (!$this->hasPermission('TOUR_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm tour';
            $this->redirect('/admin/tours');
            return;
        }
        
        $tourCode = $this->post('tour_code');
        $tourName = $this->post('tour_name');
        $duration = (int)$this->post('duration');
        $description = $this->post('description');
        $includedServices = $this->post('included_services');
        
        if (empty($tourCode) || empty($tourName) || $duration <= 0) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/admin/tours/create');
            return;
        }
        
        $db = db();
        $sql = "INSERT INTO Tours (TourCode, TourName, Duration, Description, IncludedServices, IsActive, CreatedAt) 
                VALUES (:code, :name, :duration, :desc, :services, 1, GETDATE())";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':code' => $tourCode,
            ':name' => $tourName,
            ':duration' => $duration,
            ':desc' => $description,
            ':services' => $includedServices
        ]);
        
        if ($result) {
            $tourId = $db->query('SELECT SCOPE_IDENTITY() as id')->fetch()['id'];

            // ====== XỬ LÝ ẢNH UPLOAD ======
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = __DIR__ . '/../../public/uploads/tours/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $isThumbnail = 1;
                $sortOrder   = 1;
                foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
                    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts)) continue;
                    $fileName = 'tour_' . $tourId . '_' . time() . '_' . $i . '.' . $ext;
                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($tmpName, $destPath)) {
                        $imageUrl = '/uploads/tours/' . $fileName;
                        $caption  = $_POST['captions'][$i] ?? '';
                        $sqlImg = "INSERT INTO TourImages (TourID, ImageURL, IsThumbnail, SortOrder, Caption, CreatedAt) VALUES (:tourId, :url, :thumb, :sort, :caption, GETDATE())";
                        $stmtImg = $db->prepare($sqlImg);
                        $stmtImg->execute([':tourId' => $tourId, ':url' => $imageUrl, ':thumb' => $isThumbnail, ':sort' => $sortOrder, ':caption' => $caption]);
                        $isThumbnail = 0;
                        $sortOrder++;
                    }
                }
            }
            // ================================

            $_SESSION['success'] = 'Thêm tour thành công';
        } else {
            $_SESSION['error'] = 'Thêm tour thất bại';
        }
        $this->redirect('/admin/tours');
    }
    
    public function edit($id = null) {
        if (!$this->hasPermission('TOUR_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa tour';
            $this->redirect('/admin/tours');
            return;
        }
        
        $tour = $this->tourModel->getById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour';
            $this->redirect('/admin/tours');
            return;
        }
        $images = $this->tourModel->getImages($id);
        $this->view('admin.tours.edit', ['tour' => $tour, 'images' => $images]);
    }
    
    public function update($id = null) {
        if (!$this->isPost()) {
            $this->redirect('/admin/tours');
            return;
        }
        
        if (!$this->hasPermission('TOUR_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa tour';
            $this->redirect('/admin/tours');
            return;
        }
        
        $tourCode = $this->post('tour_code');
        $tourName = $this->post('tour_name');
        $duration = (int)$this->post('duration');
        $description = $this->post('description');
        $includedServices = $this->post('included_services');
        $isActive = $this->post('is_active') ? 1 : 0;
        
        $db = db();
        $sql = "UPDATE Tours SET TourCode = :code, TourName = :name, Duration = :duration, 
                Description = :desc, IncludedServices = :services, IsActive = :active 
                WHERE TourID = :id";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':code' => $tourCode,
            ':name' => $tourName,
            ':duration' => $duration,
            ':desc' => $description,
            ':services' => $includedServices,
            ':active' => $isActive,
            ':id' => $id
        ]);
        
        if ($result) {
            // ====== UPLOAD ẢNH MỚI ======
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = realpath(__DIR__ . '/../../public/uploads/tours') . DIRECTORY_SEPARATOR;
                if (!$uploadDir || !is_dir($uploadDir)) {
                    mkdir(__DIR__ . '/../../public/uploads/tours', 0755, true);
                    $uploadDir = realpath(__DIR__ . '/../../public/uploads/tours') . DIRECTORY_SEPARATOR;
                }
                $debugInfo = [];
                $debugInfo[] = 'uploadDir: ' . $uploadDir;
                $debugInfo[] = 'is_dir: ' . (is_dir($uploadDir) ? 'yes' : 'no');
                $debugInfo[] = 'is_writable: ' . (is_writable($uploadDir) ? 'yes' : 'no');
                $debugInfo[] = 'files count: ' . count($_FILES['images']['name']);
                $debugInfo[] = 'file[0] error: ' . $_FILES['images']['error'][0];
                $debugInfo[] = 'file[0] name: ' . $_FILES['images']['name'][0];
                $debugInfo[] = 'file[0] tmp: ' . $_FILES['images']['tmp_name'][0];
                $debugInfo[] = 'tmp exists: ' . (file_exists($_FILES['images']['tmp_name'][0]) ? 'yes' : 'no');
                $_SESSION['upload_debug'] = implode(' | ', $debugInfo);

                $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $stmtCheck = $db->prepare("SELECT COUNT(*) as cnt FROM TourImages WHERE TourID = :id AND IsThumbnail = 1");
                $stmtCheck->execute([':id' => $id]);
                $hasThumb  = (int)$stmtCheck->fetch()['cnt'] > 0;
                $stmtMax   = $db->prepare("SELECT ISNULL(MAX(SortOrder), 0) as maxSort FROM TourImages WHERE TourID = :id");
                $stmtMax->execute([':id' => $id]);
                $sortOrder = (int)$stmtMax->fetch()['maxSort'] + 1;
                foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
                    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts)) continue;
                    $fileName = 'tour_' . $id . '_' . time() . '_' . $i . '.' . $ext;
                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($tmpName, $destPath)) {
                        $imageUrl    = '/uploads/tours/' . $fileName;
                        $caption     = $_POST['captions'][$i] ?? '';
                        $isThumbnail = (!$hasThumb) ? 1 : 0;
                        $sqlImg = "INSERT INTO TourImages (TourID, ImageURL, IsThumbnail, SortOrder, Caption, CreatedAt) VALUES (:tourId, :url, :thumb, :sort, :caption, GETDATE())";
                        $stmtImg = $db->prepare($sqlImg);
                        $stmtImg->execute([':tourId' => $id, ':url' => $imageUrl, ':thumb' => $isThumbnail, ':sort' => $sortOrder, ':caption' => $caption]);
                        $hasThumb  = true;
                        $sortOrder++;
                    }
                }
            }
            // ====== XÓA ẢNH ======
            $deleteIds = $_POST['delete_images'] ?? [];
            if (!empty($deleteIds)) {
                foreach ($deleteIds as $imageId) {
                    $stmtGet = $db->prepare("SELECT ImageURL, IsThumbnail FROM TourImages WHERE ImageID = :imgId AND TourID = :tourId");
                    $stmtGet->execute([':imgId' => $imageId, ':tourId' => $id]);
                    $img = $stmtGet->fetch();
                    if ($img) {
                        $filePath = __DIR__ . '/../../../public' . $img['ImageURL'];
                        if (file_exists($filePath)) unlink($filePath);
                        $db->prepare("DELETE FROM TourImages WHERE ImageID = :imgId")->execute([':imgId' => $imageId]);
                        if ($img['IsThumbnail']) {
                            $db->prepare("UPDATE TourImages SET IsThumbnail = 1 WHERE TourID = :tourId AND ImageID = (SELECT MIN(ImageID) FROM TourImages WHERE TourID = :tourId2)")->execute([':tourId' => $id, ':tourId2' => $id]);
                        }
                    }
                }
            }
            // ====== ĐẶT THUMBNAIL ======
            $newThumb = $_POST['set_thumbnail'] ?? null;
            if ($newThumb) {
                $db->prepare("UPDATE TourImages SET IsThumbnail = 0 WHERE TourID = :tourId")->execute([':tourId' => $id]);
                $db->prepare("UPDATE TourImages SET IsThumbnail = 1 WHERE ImageID = :imgId AND TourID = :tourId")->execute([':imgId' => $newThumb, ':tourId' => $id]);
            }

            $_SESSION['success'] = 'Cập nhật tour thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật tour thất bại';
        }
        $this->redirect('/admin/tours/edit/' . $id);
    }
    
    public function delete($id = null) {
        if (!$this->hasPermission('TOUR_DELETE')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa tour';
            $this->redirect('/admin/tours');
            return;
        }
        
        $db = db();
        $sql = "UPDATE Tours SET IsActive = 0 WHERE TourID = :id";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([':id' => $id]);
        
        if ($result) {
            $_SESSION['success'] = 'Xóa tour thành công';
        } else {
            $_SESSION['error'] = 'Xóa tour thất bại';
        }
        $this->redirect('/admin/tours');
    }
    
    // ==================== QUẢN LÝ LỊCH KHỞI HÀNH ====================
    
    public function schedules() {
        if (!$this->hasPermission('SCHEDULE_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem lịch khởi hành';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $scheduleModel = new Schedule();
        $schedules = $scheduleModel->getAll();
        $this->view('admin.schedules.index', ['schedules' => $schedules]);
    }
    
    public function scheduleCreate() {
        if (!$this->hasPermission('SCHEDULE_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $scheduleModel = new Schedule();
        $tours = $scheduleModel->getTours();
        $this->view('admin.schedules.create', ['tours' => $tours]);
    }
    
    public function scheduleStore() {
        if (!$this->isPost()) {
            $this->redirect('/admin/schedules');
            return;
        }
        
        if (!$this->hasPermission('SCHEDULE_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $tourId = (int)$this->post('tour_id');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $price = (float)$this->post('price');
        $totalSlots = (int)$this->post('total_slots');
        
        if ($tourId <= 0 || empty($startDate) || empty($endDate) || $price <= 0 || $totalSlots <= 0) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/admin/schedule/create');
            return;
        }
        
        $scheduleModel = new Schedule();
        $result = $scheduleModel->create($tourId, $startDate, $endDate, $price, $totalSlots);
        
        if ($result) {
            $_SESSION['success'] = 'Thêm lịch khởi hành thành công';
        } else {
            $_SESSION['error'] = 'Thêm lịch khởi hành thất bại';
        }
        $this->redirect('/admin/schedules');
    }
    
    public function scheduleEdit($id = null) {
        if (!$this->hasPermission('SCHEDULE_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $scheduleModel = new Schedule();
        $schedule = $scheduleModel->getById($id);
        $tours = $scheduleModel->getTours();
        
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $this->view('admin.schedules.edit', [
            'schedule' => $schedule,
            'tours' => $tours
        ]);
    }
    
    public function scheduleUpdate($id = null) {
        if (!$this->isPost()) {
            $this->redirect('/admin/schedules');
            return;
        }
        
        if (!$this->hasPermission('SCHEDULE_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $tourId = (int)$this->post('tour_id');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $price = (float)$this->post('price');
        $totalSlots = (int)$this->post('total_slots');
        $status = $this->post('status');
        
        $scheduleModel = new Schedule();
        $result = $scheduleModel->update($id, $tourId, $startDate, $endDate, $price, $totalSlots, $status);
        
        if ($result) {
            $_SESSION['success'] = 'Cập nhật lịch khởi hành thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật lịch khởi hành thất bại';
        }
        $this->redirect('/admin/schedules');
    }
    
    public function scheduleDelete($id = null) {
        if (!$this->hasPermission('SCHEDULE_DELETE')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa lịch khởi hành';
            $this->redirect('/admin/schedules');
            return;
        }
        
        $scheduleModel = new Schedule();
        $result = $scheduleModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Xóa lịch khởi hành thành công';
        } else {
            $_SESSION['error'] = 'Xóa lịch khởi hành thất bại';
        }
        $this->redirect('/admin/schedules');
    }
    
    // ==================== QUẢN LÝ VOUCHER ====================
    
    public function vouchers() {
        if (!$this->hasPermission('VOUCHER_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem voucher';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $voucherModel = new Voucher();
        $vouchers = $voucherModel->getAll();
        $this->view('admin.vouchers.index', ['vouchers' => $vouchers]);
    }
    
    public function voucherCreate() {
        if (!$this->hasPermission('VOUCHER_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        $this->view('admin.vouchers.create');
    }
    
    public function voucherStore() {
        if (!$this->isPost()) {
            $this->redirect('/admin/vouchers');
            return;
        }
        
        if (!$this->hasPermission('VOUCHER_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        
        $code = $this->post('code');
        $name = $this->post('name');
        $discountValue = (float)$this->post('discount_value');
        $minOrderValue = (float)$this->post('min_order_value');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $quantity = (int)$this->post('quantity');
        
        if (empty($code) || empty($name) || $discountValue <= 0 || $quantity <= 0) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/admin/voucher/create');
            return;
        }
        
        $voucherModel = new Voucher();
        $result = $voucherModel->create($code, $name, $discountValue, $minOrderValue, $startDate, $endDate, $quantity);
        
        if ($result) {
            $_SESSION['success'] = 'Thêm voucher thành công';
        } else {
            $_SESSION['error'] = 'Thêm voucher thất bại';
        }
        $this->redirect('/admin/vouchers');
    }
    
    public function voucherEdit($id = null) {
        if (!$this->hasPermission('VOUCHER_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        
        $voucherModel = new Voucher();
        $voucher = $voucherModel->getById($id);
        
        if (!$voucher) {
            $_SESSION['error'] = 'Không tìm thấy voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        
        $this->view('admin.vouchers.edit', ['voucher' => $voucher]);
    }
    
    public function voucherUpdate($id = null) {
        if (!$this->isPost()) {
            $this->redirect('/admin/vouchers');
            return;
        }
        
        if (!$this->hasPermission('VOUCHER_EDIT')) {
            $_SESSION['error'] = 'Bạn không có quyền sửa voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        
        $code = $this->post('code');
        $name = $this->post('name');
        $discountValue = (float)$this->post('discount_value');
        $minOrderValue = (float)$this->post('min_order_value');
        $startDate = $this->post('start_date');
        $endDate = $this->post('end_date');
        $quantity = (int)$this->post('quantity');
        $isActive = (int)$this->post('is_active');
        
        $voucherModel = new Voucher();
        $result = $voucherModel->update($id, $code, $name, $discountValue, $minOrderValue, $startDate, $endDate, $quantity, $isActive);
        
        if ($result) {
            $_SESSION['success'] = 'Cập nhật voucher thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật voucher thất bại';
        }
        $this->redirect('/admin/vouchers');
    }
    
    public function voucherDelete($id = null) {
        if (!$this->hasPermission('VOUCHER_DELETE')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa voucher';
            $this->redirect('/admin/vouchers');
            return;
        }
        
        $voucherModel = new Voucher();
        $result = $voucherModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Xóa voucher thành công';
        } else {
            $_SESSION['error'] = 'Xóa voucher thất bại';
        }
        $this->redirect('/admin/vouchers');
    }
    
    // ==================== QUẢN LÝ BOOKING ====================
    
    public function bookings() {
        if (!$this->hasPermission('BOOKING_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem booking';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $bookings = $bookingModel->getAll();
        $this->view('admin.bookings.index', ['bookings' => $bookings]);
    }
    
    public function bookingDetail($id = null) {
        if (!$this->hasPermission('BOOKING_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem booking';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $booking = $bookingModel->getById($id);
        
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $db = db();
        $stmt = $db->prepare("SELECT * FROM BookingPassengers WHERE BookingID = :id");
        $stmt->execute([':id' => $id]);
        $passengers = $stmt->fetchAll();
        
        $this->view('admin.bookings.detail', [
            'booking' => $booking,
            'passengers' => $passengers
        ]);
    }
    
    public function bookingConfirm($id = null) {
        if (!$this->hasPermission('BOOKING_CONFIRM')) {
            $_SESSION['error'] = 'Bạn không có quyền duyệt booking';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $result = $bookingModel->updateStatus($id, 'Đã xác nhận');
        
        if ($result) {
            $_SESSION['success'] = 'Đã xác nhận booking thành công';
        } else {
            $_SESSION['error'] = 'Xác nhận booking thất bại';
        }
        $this->redirect('/admin/bookings');
    }
    
    public function bookingCancel($id = null) {
        if (!$this->hasPermission('BOOKING_CANCEL')) {
            $_SESSION['error'] = 'Bạn không có quyền hủy booking';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $result = $bookingModel->updateStatus($id, 'Đã hủy');
        
        if ($result) {
            $_SESSION['success'] = 'Đã hủy booking thành công';
        } else {
            $_SESSION['error'] = 'Hủy booking thất bại';
        }
        $this->redirect('/admin/bookings');
    }
    
    public function bookingPaymentConfirm($id = null) {
        if (!$this->hasPermission('PAYMENT_CONFIRM')) {
            $_SESSION['error'] = 'Bạn không có quyền xác nhận thanh toán';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $r1 = $bookingModel->updatePaymentStatus($id, 'Đã thanh toán');
        // Đồng thời cập nhật BookingStatus -> Đã xác nhận nếu đang chờ
        $db = db();
        $db->prepare("UPDATE Bookings SET BookingStatus = 'Đã xác nhận' WHERE BookingID = :id AND BookingStatus = 'Chờ xác nhận'")
           ->execute([':id' => $id]);
        
        if ($r1) {
            $_SESSION['success'] = 'Đã xác nhận thanh toán thành công';
        } else {
            $_SESSION['error'] = 'Xác nhận thanh toán thất bại';
        }
        $this->redirect('/admin/bookings/detail/' . $id);
    }

    // Đánh dấu tour hoàn thành → khách có thể đánh giá
    public function bookingComplete($id = null) {
        if (!$this->hasPermission('BOOKING_CONFIRM')) {
            $_SESSION['error'] = 'Bạn không có quyền cập nhật booking';
            $this->redirect('/admin/bookings');
            return;
        }
        
        $db = db();
        $db->prepare("UPDATE Bookings SET BookingStatus = 'Hoàn thành' WHERE BookingID = :id")
           ->execute([':id' => $id]);
        
        $_SESSION['success'] = 'Đã đánh dấu tour hoàn thành. Khách hàng có thể đánh giá.';
        $this->redirect('/admin/bookings/detail/' . $id);
    }
    
    // ==================== QUẢN LÝ HÓA ĐƠN (MỚI) ====================
    
    public function invoices() {
        if (!$this->hasPermission('INVOICE_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $invoiceModel = new Invoice();
        $invoices = $invoiceModel->getAll();
        $this->view('admin.invoices.index', ['invoices' => $invoices]);
    }
    
    public function invoiceCreate() {
        if (!$this->hasPermission('INVOICE_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        $bookingModel = new BookingAdmin();
        $bookings = $bookingModel->getAll();
        $this->view('admin.invoices.create', ['bookings' => $bookings]);
    }
    
    public function invoiceStore() {
        if (!$this->isPost()) {
            $this->redirect('/admin/invoices');
            return;
        }
        
        if (!$this->hasPermission('INVOICE_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        $bookingId = (int)$this->post('booking_id');
        $invoiceNumber = 'INV' . date('YmdHis') . rand(100, 999);
        
        $invoiceModel = new Invoice();
        $result = $invoiceModel->create($invoiceNumber, $bookingId);
        
        if ($result) {
            $_SESSION['success'] = 'Tạo hóa đơn thành công';
        } else {
            $_SESSION['error'] = 'Tạo hóa đơn thất bại';
        }
        $this->redirect('/admin/invoices');
    }
    
    // ==================== QUẢN LÝ ĐÁNH GIÁ (MỚI) ====================
    
    public function reviews() {
        if (!$this->hasPermission('REVIEW_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem đánh giá';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $reviewModel = new Review();
        $reviews = $reviewModel->getAll();
        $this->view('admin.reviews.index', ['reviews' => $reviews]);
    }
    
    public function reviewApprove($id = null) {
        if (!$this->hasPermission('REVIEW_APPROVE')) {
            $_SESSION['error'] = 'Bạn không có quyền duyệt đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $reviewModel = new Review();
        $reviewModel->updateVisibility($id, 1);
        $_SESSION['success'] = 'Đã duyệt đánh giá';
        $this->redirect('/admin/reviews');
    }
    
    public function reviewHide($id = null) {
        if (!$this->hasPermission('REVIEW_HIDE')) {
            $_SESSION['error'] = 'Bạn không có quyền ẩn đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $reviewModel = new Review();
        $reviewModel->updateVisibility($id, 0);
        $_SESSION['success'] = 'Đã ẩn đánh giá';
        $this->redirect('/admin/reviews');
    }
    
    public function reviewDelete($id = null) {
        if (!$this->hasPermission('REVIEW_DELETE')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $reviewModel = new Review();
        $reviewModel->delete($id);
        $_SESSION['success'] = 'Đã xóa đánh giá';
        $this->redirect('/admin/reviews');
    }
    
    // ==================== CRM & TASKS (MỚI) ====================
    
    public function crm() {
        if (!$this->hasPermission('CRM_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem CRM';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $crmLogModel = new CRMLog();
        $logs = $crmLogModel->getAll();
        $this->view('admin.crm.logs', ['logs' => $logs]);
    }
    
    public function crmCreateLog() {
        if (!$this->hasPermission('CRM_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm ghi chú CRM';
            $this->redirect('/admin/crm');
            return;
        }
        
        $customerModel = new Customer();
        $customers = $customerModel->getAll();
        $this->view('admin.crm.create_log', ['customers' => $customers]);
    }
    
    public function crmStoreLog() {
        if (!$this->isPost()) {
            $this->redirect('/admin/crm');
            return;
        }
        
        if (!$this->hasPermission('CRM_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm ghi chú CRM';
            $this->redirect('/admin/crm');
            return;
        }
        
        $customerId = (int)$this->post('customer_id');
        $interactionType = $this->post('interaction_type');
        $content = $this->post('content');
        $employeeId = $_SESSION['user']['UserID'];
        
        $crmLogModel = new CRMLog();
        $crmLogModel->create($customerId, $employeeId, $interactionType, $content);
        $_SESSION['success'] = 'Đã thêm ghi chú CRM';
        $this->redirect('/admin/crm/logs');
    }
    
    public function tasks() {
        if (!$this->hasPermission('CRM_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem tasks';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $taskModel = new Task();
        $tasks = $taskModel->getAll();
        $this->view('admin.crm.tasks', ['tasks' => $tasks]);
    }
    
    public function taskCreate() {
        if (!$this->hasPermission('CRM_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm task';
            $this->redirect('/admin/crm');
            return;
        }
        
        $customerModel = new Customer();
        $customers = $customerModel->getAll();
        $this->view('admin.crm.create_task', ['customers' => $customers]);
    }
    
    public function taskStore() {
        if (!$this->isPost()) {
            $this->redirect('/admin/crm');
            return;
        }
        
        if (!$this->hasPermission('CRM_CREATE')) {
            $_SESSION['error'] = 'Bạn không có quyền thêm task';
            $this->redirect('/admin/crm');
            return;
        }
        
        $customerId = (int)$this->post('customer_id');
        $title = $this->post('title');
        $dueDate = $this->post('due_date');
        $employeeId = $_SESSION['user']['UserID'];
        
        $taskModel = new Task();
        $taskModel->create($employeeId, $customerId, $title, $dueDate);
        $_SESSION['success'] = 'Đã tạo task mới';
        $this->redirect('/admin/crm/tasks');
    }
    
    public function taskComplete($id = null) {
        if (!$this->hasPermission('CRM_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền cập nhật task';
            $this->redirect('/admin/crm');
            return;
        }
        
        $taskModel = new Task();
        $taskModel->markCompleted($id);
        $_SESSION['success'] = 'Đã hoàn thành task';
        $this->redirect('/admin/crm/tasks');
    }
    
    // ==================== QUẢN LÝ NGƯỜI DÙNG ====================
    
    public function users() {
        if (!$this->hasRole(['Admin', 'Manager'])) {
            $_SESSION['error'] = 'Bạn không có quyền xem người dùng';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $userModel = new UserAdmin();
        $users = $userModel->getAll();
        $this->view('admin.users.index', ['users' => $users]);
    }
    
    public function userCreate() {
        if (!$this->hasRole(['Admin', 'Manager'])) {
            $_SESSION['error'] = 'Bạn không có quyền thêm người dùng';
            $this->redirect('/admin/users');
            return;
        }
        
        $db = db();
        $stmt = $db->query("SELECT RoleID, RoleName FROM Roles WHERE RoleName IN ('Sale', 'Manager', 'Accountant')");
        $roles = $stmt->fetchAll();
        $this->view('admin.users.create', ['roles' => $roles]);
    }
    
    public function userStore() {
        if (!$this->isPost()) {
            $this->redirect('/admin/users');
            return;
        }
        
        if (!$this->hasRole(['Admin', 'Manager'])) {
            $_SESSION['error'] = 'Bạn không có quyền thêm người dùng';
            $this->redirect('/admin/users');
            return;
        }
        
        $username = $this->post('username');
        $password = $this->post('password');
        $fullname = $this->post('fullname');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $roleId = (int)$this->post('role_id');
        
        if (empty($username) || empty($password) || empty($fullname) || empty($email) || $roleId <= 0) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/admin/users/create');
            return;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $db = db();
        
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Users WHERE Username = :user OR Email = :email");
        $stmt->execute([':user' => $username, ':email' => $email]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            $_SESSION['error'] = 'Tên đăng nhập hoặc email đã tồn tại';
            $this->redirect('/admin/users/create');
            return;
        }
        
        $sql = "INSERT INTO Users (Username, PasswordHash, FullName, Email, Phone, RoleID, IsActive, CreatedAt) 
                VALUES (:user, :pass, :name, :email, :phone, :role, 1, GETDATE())";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':user' => $username,
            ':pass' => $hashedPassword,
            ':name' => $fullname,
            ':email' => $email,
            ':phone' => $phone,
            ':role' => $roleId
        ]);
        
        if ($result) {
            $_SESSION['success'] = 'Thêm người dùng thành công';
        } else {
            $_SESSION['error'] = 'Thêm người dùng thất bại';
        }
        $this->redirect('/admin/users');
    }
    
    public function userToggle($id = null) {
        if (!$this->hasRole(['Admin', 'Manager'])) {
            $_SESSION['error'] = 'Bạn không có quyền khóa/mở tài khoản';
            $this->redirect('/admin/users');
            return;
        }
        
        $userModel = new UserAdmin();
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy người dùng';
            $this->redirect('/admin/users');
            return;
        }
        
        $newStatus = $user['IsActive'] ? 0 : 1;
        $result = $userModel->toggleActive($id, $newStatus);
        
        if ($result) {
            $_SESSION['success'] = $newStatus ? 'Đã mở khóa tài khoản' : 'Đã khóa tài khoản';
        } else {
            $_SESSION['error'] = 'Thao tác thất bại';
        }
        $this->redirect('/admin/users');
    }
    
    public function userDelete($id = null) {
        if (!$this->hasRole('Admin')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa người dùng';
            $this->redirect('/admin/users');
            return;
        }
        
        $userModel = new UserAdmin();
        $result = $userModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Xóa người dùng thành công';
        } else {
            $_SESSION['error'] = 'Xóa người dùng thất bại';
        }
        $this->redirect('/admin/users');
    }
    
    // ==================== BÁO CÁO THỐNG KÊ ====================
    
    public function reports() {
        if (!$this->hasPermission('REPORT_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem báo cáo';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $reportModel = new Report();
        $summary = $reportModel->getSummary();
        $currentYear = date('Y');
        $monthlyRevenue = $reportModel->getMonthlyRevenue($currentYear);
        $topTours = $reportModel->getTopTours(10);
        
        $this->view('admin.reports.index', [
            'summary' => $summary,
            'currentYear' => $currentYear,
            'monthlyRevenue' => $monthlyRevenue,
            'topTours' => $topTours
        ]);
    }
    
    public function reportRevenue() {
        if (!$this->hasPermission('REPORT_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem báo cáo';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $year = (int)($_GET['year'] ?? date('Y'));
        $reportModel = new Report();
        $monthlyRevenue = $reportModel->getMonthlyRevenue($year);
        
        header('Content-Type: application/json');
        echo json_encode([
            'year' => $year,
            'data' => array_values($monthlyRevenue)
        ]);
        exit();
    }
    
    public function reportExportRevenue() {
        if (!$this->hasPermission('REPORT_EXPORT')) {
            $_SESSION['error'] = 'Bạn không có quyền xuất báo cáo';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $year = (int)($_GET['year'] ?? date('Y'));
        $reportModel = new Report();
        $monthlyRevenue = $reportModel->getMonthlyRevenue($year);
        $summary = $reportModel->getSummary();
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_doanhthu_' . $year . '.xls"');
        
        echo "<table border='1'>";
        echo "<caption><h2>BÁO CÁO DOANH THU NĂM $year</h2></caption>";
        echo "<tr><th>Tháng</th><th>Doanh thu (VNĐ)</th>";
        for ($i = 1; $i <= 12; $i++) {
            echo "<tr>";
            echo "<td>Tháng $i</td>";
            echo "<td>" . number_format($monthlyRevenue[$i], 0, ',', '.') . "đ</td>";
            echo "</tr>";
        }
        echo "<tr><th>Tổng doanh thu</th><th>" . number_format($summary['total_revenue'], 0, ',', '.') . "đ</th></tr>";
        echo "</table>";
        exit();
    }
}
?>