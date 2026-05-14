<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Tour.php';
require_once __DIR__ . '/../Models/Schedule.php';
require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/../Models/BookingAdmin.php';
require_once __DIR__ . '/../Models/UserAdmin.php';
require_once __DIR__ . '/../Models/Report.php';

class AdminController extends BaseController {
    
    private Tour $tourModel;
    
    public function __construct() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['RoleName'] !== 'Admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            $this->redirect('/auth/login');
            exit();
        }
        $this->tourModel = new Tour();
    }
    
    // ==================== DASHBOARD ====================
    
    public function dashboard() {
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
        $db = db();
        $stmt = $db->query("SELECT * FROM Tours ORDER BY TourID DESC");
        $tours = $stmt->fetchAll();
        $this->view('admin.tours.index', ['tours' => $tours]);
    }
    
    public function create() {
        $this->view('admin.tours.create');
    }
    
    public function store() {
        if (!$this->isPost()) {
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
            $_SESSION['success'] = 'Thêm tour thành công';
        } else {
            $_SESSION['error'] = 'Thêm tour thất bại';
        }
        $this->redirect('/admin/tours');
    }
    
    public function edit($id = null) {
        $tour = $this->tourModel->getById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour';
            $this->redirect('/admin/tours');
            return;
        }
        $this->view('admin.tours.edit', ['tour' => $tour]);
    }
    
    public function update($id = null) {
        if (!$this->isPost()) {
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
            $_SESSION['success'] = 'Cập nhật tour thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật tour thất bại';
        }
        $this->redirect('/admin/tours');
    }
    
    public function delete($id = null) {
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
        $scheduleModel = new Schedule();
        $schedules = $scheduleModel->getAll();
        $this->view('admin.schedules.index', ['schedules' => $schedules]);
    }
    
    public function scheduleCreate() {
        $scheduleModel = new Schedule();
        $tours = $scheduleModel->getTours();
        $this->view('admin.schedules.create', ['tours' => $tours]);
    }
    
    public function scheduleStore() {
        if (!$this->isPost()) {
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
        $voucherModel = new Voucher();
        $vouchers = $voucherModel->getAll();
        $this->view('admin.vouchers.index', ['vouchers' => $vouchers]);
    }
    
    public function voucherCreate() {
        $this->view('admin.vouchers.create');
    }
    
    public function voucherStore() {
        if (!$this->isPost()) {
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
        $bookingModel = new BookingAdmin();
        $bookings = $bookingModel->getAll();
        $this->view('admin.bookings.index', ['bookings' => $bookings]);
    }
    
    public function bookingDetail($id = null) {
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
        $bookingModel = new BookingAdmin();
        $result = $bookingModel->updatePaymentStatus($id, 'Đã thanh toán');
        
        if ($result) {
            $_SESSION['success'] = 'Đã xác nhận thanh toán thành công';
        } else {
            $_SESSION['error'] = 'Xác nhận thanh toán thất bại';
        }
        $this->redirect('/admin/bookings');
    }
    
    // ==================== QUẢN LÝ NGƯỜI DÙNG ====================
    
    public function users() {
        $userModel = new UserAdmin();
        $users = $userModel->getAll();
        $this->view('admin.users.index', ['users' => $users]);
    }
    
    public function userCreate() {
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
        $year = (int)($_GET['year'] ?? date('Y'));
        $reportModel = new Report();
        $monthlyRevenue = $reportModel->getMonthlyRevenue($year);
        $summary = $reportModel->getSummary();
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="baocao_doanhthu_' . $year . '.xls"');
        
        echo "<table border='1'>";
        echo "<caption><h2>BÁO CÁO DOANH THU NĂM $year</h2></caption>";
        echo "<tr><th>Tháng</th><th>Doanh thu (VNĐ)</th>不大";
        for ($i = 1; $i <= 12; $i++) {
            echo "<tr>";
            echo "</td>Tháng $i</td>";
            echo "<td>" . number_format($monthlyRevenue[$i], 0, ',', '.') . "đ</td>";
            echo "</tr>";
        }
        echo "<tr><th>Tổng doanh thu</th><th>" . number_format($summary['total_revenue'], 0, ',', '.') . "đ</th></tr>";
        echo "</table>";
        exit();
    }
}
?>