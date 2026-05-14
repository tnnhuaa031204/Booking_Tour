<?php
// src/Controllers/BookingController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Tour.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/BookingPassenger.php';
require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/../Services/EmailService.php'; // THÊM DÒNG NÀY

class BookingController extends BaseController {
    
    private Tour $tourModel;
    private Booking $bookingModel;
    private BookingPassenger $passengerModel;
    private EmailService $emailService; // THÊM BIẾN NÀY
    
    public function __construct() {
        $this->tourModel = new Tour();
        $this->bookingModel = new Booking();
        $this->passengerModel = new BookingPassenger();
        $this->emailService = new EmailService(); // THÊM DÒNG NÀY
    }
    
    // Hiển thị form đặt tour
    public function create($id = null) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đặt tour';
            $this->redirect('/auth/login');
            return;
        }
        
        $tourId = $id;
        $scheduleId = $_GET['schedule_id'] ?? null;
        
        $db = db();
        $tour = null;
        $schedule = null;
        
        if ($scheduleId) {
            $stmt = $db->prepare("SELECT ts.*, t.TourID, t.TourName, t.TourCode, t.Duration, t.Description
                                  FROM TourSchedules ts
                                  JOIN Tours t ON ts.TourID = t.TourID
                                  WHERE ts.ScheduleID = :id");
            $stmt->execute([':id' => $scheduleId]);
            $schedule = $stmt->fetch();
            if ($schedule) {
                $tour = $schedule;
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->getById($tourId);
        }
        
        if (!$tour) {
            http_response_code(404);
            echo "404 - Không tìm thấy tour";
            return;
        }
        
        $this->view('booking.create', [
            'tour' => $tour,
            'schedule' => $schedule
        ]);
    }
    
    // AJAX áp dụng voucher
    public function applyVoucher() {
        // Xóa buffer output để tránh lỗi JSON
        ob_clean();
        
        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $voucherCode = $this->post('voucher_code');
        $totalAmount = (float)$this->post('total_amount');
        
        $voucherModel = new Voucher();
        $result = $voucherModel->applyDiscount($voucherCode, $totalAmount);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    
    // Xử lý đặt tour
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/');
            return;
        }
        
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đặt tour';
            $this->redirect('/auth/login');
            return;
        }
        
        $userId = $_SESSION['user']['UserID'];
        
        $db = db();
        $stmt = $db->prepare("SELECT CustomerID FROM Customers WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $customer = $stmt->fetch();
        
        if (!$customer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng';
            $this->redirect('/');
            return;
        }
        
        $customerId = $customer['CustomerID'];
        $scheduleId = $this->post('schedule_id');
        $adultCount = (int)$this->post('adult_count');
        $childCount = (int)$this->post('child_count');
        $notes = $this->post('notes');
        $voucherCode = $this->post('final_voucher_code');
        $discountAmount = (float)$this->post('discount_amount');
        
        $stmt = $db->prepare("SELECT Price FROM TourSchedules WHERE ScheduleID = :id");
        $stmt->execute([':id' => $scheduleId]);
        $schedule = $stmt->fetch();
        
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành';
            $this->redirect('/');
            return;
        }
        
        $price = $schedule['Price'];
        $totalAmount = ($adultCount * $price) + ($childCount * $price * 0.7);
        $finalAmount = $totalAmount - $discountAmount;
        if ($finalAmount < 0) $finalAmount = 0;
        
        // Lấy voucher_id từ mã code
        $voucherId = null;
        if (!empty($voucherCode)) {
            $voucherModel = new Voucher();
            $voucher = $voucherModel->getByCode($voucherCode);
            if ($voucher) {
                $voucherId = $voucher['VoucherID'];
                $voucherModel->incrementUsed($voucherId);
            }
        }
        
        // Tạo booking
        $bookingId = $this->bookingModel->create($customerId, $scheduleId, $adultCount, $childCount, $finalAmount, $notes);
        
        if (!$bookingId) {
            $_SESSION['error'] = 'Đặt tour thất bại, vui lòng thử lại';
            $this->redirect('/');
            return;
        }
        
        // Lưu thông tin voucher sử dụng
        if ($voucherId && $discountAmount > 0) {
            $stmt = $db->prepare("INSERT INTO VoucherUsages (VoucherID, BookingID, CustomerID, DiscountAmount, UsedAt) 
                                  VALUES (:voucherId, :bookingId, :customerId, :discount, GETDATE())");
            $stmt->execute([
                ':voucherId' => $voucherId,
                ':bookingId' => $bookingId,
                ':customerId' => $customerId,
                ':discount' => $discountAmount
            ]);
        }
        
        // Lưu danh sách khách đi tour
        $passengers = [];
        $names = $this->post('passenger_name') ?? [];
        $types = $this->post('passenger_type') ?? [];
        
        for ($i = 0; $i < count($names); $i++) {
            if (!empty($names[$i])) {
                $passengers[] = [
                    'fullname' => $names[$i],
                    'type' => $types[$i] ?? 'Adult',
                    'gender' => null,
                    'dob' => null
                ];
            }
        }
        
        if (!empty($passengers)) {
            $this->passengerModel->addPassengers($bookingId, $passengers);
        }
        
        // ===== BẮT ĐẦU: GỬI EMAIL XÁC NHẬN =====
        // Lấy thông tin chi tiết để gửi email
        $booking = $this->bookingModel->getById($bookingId);
        $tour = $this->tourModel->getByScheduleId($scheduleId);
        $customerEmail = $_SESSION['user']['Email'];
        $customerName = $_SESSION['user']['FullName'];
        
        // Chuẩn bị dữ liệu cho email
        $bookingData = [
            'booking_code' => $booking['BookingCode'] ?? 'BK' . $bookingId,
            'tour_name' => $tour['TourName'] ?? 'Tour du lịch',
            'start_date' => date('d/m/Y', strtotime($schedule['StartDate'] ?? date('Y-m-d'))),
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'total_amount' => $totalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'payment_status' => 'Chưa thanh toán'
        ];
        
        // Gửi email
        try {
            $this->emailService->sendBookingConfirmation(
                $customerEmail,
                $customerName,
                $bookingData
            );
        } catch (Exception $e) {
            // Log lỗi nhưng không ảnh hưởng đến người dùng
            error_log('Lỗi gửi email: ' . $e->getMessage());
        }
        // ===== KẾT THÚC: GỬI EMAIL XÁC NHẬN =====
        
        $_SESSION['success'] = 'Đặt tour thành công! Mã booking: BK' . $bookingId;
        $_SESSION['final_amount'] = $finalAmount;
        $_SESSION['discount_amount'] = $discountAmount;
        $this->redirect("/booking/confirm?id=$bookingId");
    }
    
    // Xác nhận đặt tour (hiển thị thông tin sau khi đặt)
    public function confirm() {
        $bookingId = $_GET['id'] ?? null;
        
        if (!$bookingId) {
            $this->redirect('/');
            return;
        }
        
        $booking = $this->bookingModel->getById($bookingId);
        $passengers = $this->passengerModel->getByBooking($bookingId);
        
        if (!$booking) {
            http_response_code(404);
            echo "404 - Không tìm thấy booking";
            return;
        }
        
        $this->view('booking.confirm', [
            'booking' => $booking,
            'passengers' => $passengers
        ]);
    }
    
    // Lịch sử đặt tour
    public function history() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem lịch sử';
            $this->redirect('/auth/login');
            return;
        }
        
        $userId = $_SESSION['user']['UserID'];
        $db = db();
        $stmt = $db->prepare("SELECT CustomerID FROM Customers WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $customer = $stmt->fetch();
        
        $bookings = [];
        if ($customer) {
            $bookings = $this->bookingModel->getByCustomer($customer['CustomerID']);
        }
        
        $this->view('booking.history', ['bookings' => $bookings]);
    }
    
    // Hủy tour
    public function cancel($id = null) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            $this->redirect('/auth/login');
            return;
        }
        
        $userId = $_SESSION['user']['UserID'];
        
        $db = db();
        $stmt = $db->prepare("SELECT CustomerID FROM Customers WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        $customer = $stmt->fetch();
        
        if (!$customer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng';
            $this->redirect('/booking/history');
            return;
        }
        
        $customerId = $customer['CustomerID'];
        
        $result = $this->bookingModel->cancelBooking((int)$id, $customerId);
        
        if ($result) {
            $_SESSION['success'] = 'Hủy tour thành công';
        } else {
            $_SESSION['error'] = 'Không thể hủy tour này (đã thanh toán hoặc đã xác nhận)';
        }
        
        $this->redirect('/booking/history');
    }
}
?>