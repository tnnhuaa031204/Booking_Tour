<?php
// src/Controllers/PaymentController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Services/MomoService.php';

class PaymentController extends BaseController {
    
    private Booking $bookingModel;
    private MomoService $momoService;
    
    public function __construct() {
        $this->bookingModel = new Booking();
        $this->momoService  = new MomoService();
    }
    
    /**
     * Hiển thị trang chọn phương thức thanh toán
     */
    public function index($bookingId = null) {
        if (!$bookingId) {
            $this->redirect('/');
            return;
        }
        
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking';
            $this->redirect('/');
            return;
        }
        
        if ($booking['PaymentStatus'] === 'Đã thanh toán') {
            $_SESSION['info'] = 'Booking này đã được thanh toán';
            $this->redirect('/booking/history');
            return;
        }
        
        $this->view('payment.index', [
            'booking' => $booking,
            'amount'  => $booking['TotalAmount']
        ]);
    }
    
    /**
     * Tạo link thanh toán MoMo → chuyển hướng sang trang MoMo
     */
    public function createMomo($bookingId = null) {
        if (!$bookingId) {
            $this->redirect('/');
            return;
        }
        
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking';
            $this->redirect('/');
            return;
        }
        
        if ($booking['PaymentStatus'] === 'Đã thanh toán') {
            $_SESSION['info'] = 'Booking này đã được thanh toán';
            $this->redirect('/booking/history');
            return;
        }
        
        // Dùng domain thật nếu deploy lên server, localhost nếu dev
        $baseUrl   = 'http://localhost:8000';
        $returnUrl = $baseUrl . '/payment/callback';
        $notifyUrl = $baseUrl . '/payment/notify';
        
        $result = $this->momoService->createPayment(
            $bookingId,
            $booking['TotalAmount'],
            $returnUrl,
            $notifyUrl
        );
        
        if ($result && isset($result['payUrl']) && $result['resultCode'] == 0) {
            header('Location: ' . $result['payUrl']);
            exit();
        } else {
            $errMsg = $result['message'] ?? 'Không thể kết nối MoMo';
            $_SESSION['error'] = 'Tạo thanh toán MoMo thất bại: ' . $errMsg;
            $this->redirect('/payment/' . $bookingId);
        }
    }
    
    /**
     * Hiển thị QR thanh toán MoMo (scan bằng app MoMo)
     */
    public function qrMomo($bookingId = null) {
        if (!$bookingId) {
            $this->redirect('/');
            return;
        }
        
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking';
            $this->redirect('/');
            return;
        }
        
        if ($booking['PaymentStatus'] === 'Đã thanh toán') {
            $_SESSION['info'] = 'Booking này đã được thanh toán';
            $this->redirect('/booking/history');
            return;
        }
        
        $result = $this->momoService->createQRPayment(
            $bookingId,
            $booking['TotalAmount']
        );
        
        if ($result && isset($result['qrCodeUrl']) && $result['resultCode'] == 0) {
            $this->view('payment.momo_qr', [
                'booking' => $booking,
                'amount'  => $booking['TotalAmount'],
                'qrData'  => $result
            ]);
        } else {
            $errMsg = $result['message'] ?? 'Không thể tạo QR MoMo';
            $_SESSION['error'] = 'Tạo QR thất bại: ' . $errMsg;
            $this->redirect('/payment/' . $bookingId);
        }
    }
    
    /**
     * Callback — MoMo chuyển hướng người dùng về sau khi thanh toán
     */
    public function callback() {
        $resultCode = $_GET['resultCode'] ?? $_GET['errorCode'] ?? -1;
        $extraData  = $_GET['extraData']  ?? '';
        
        // Decode extraData (base64)
        $decoded = base64_decode($extraData);
        parse_str($decoded, $data);
        $bookingId = $data['booking_id'] ?? 0;
        
        if ($resultCode == 0 && $bookingId) {
            $this->bookingModel->updatePaymentStatus($bookingId, 'Đã thanh toán');
            $this->bookingModel->updateStatus($bookingId, 'Đã xác nhận');
            $_SESSION['success'] = 'Thanh toán MoMo thành công!';
        } else {
            $_SESSION['error'] = 'Thanh toán thất bại hoặc bị hủy.';
        }
        
        $this->redirect('/booking/history');
    }
    
    /**
     * IPN (Notify) — MoMo gửi thông báo bất đồng bộ về server
     */
    public function notify() {
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);
        
        // Ghi log
        file_put_contents(
            __DIR__ . '/../../../momo_notify.log',
            date('Y-m-d H:i:s') . ': ' . $input . PHP_EOL,
            FILE_APPEND
        );
        
        if (!$data) {
            echo json_encode(['message' => 'invalid data']);
            exit();
        }
        
        // Xác minh chữ ký
        if (!$this->momoService->verifySignature($data)) {
            echo json_encode(['message' => 'invalid signature']);
            exit();
        }
        
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            $extraData = base64_decode($data['extraData'] ?? '');
            parse_str($extraData, $extra);
            $bookingId = $extra['booking_id'] ?? 0;
            
            if ($bookingId) {
                $this->bookingModel->updatePaymentStatus($bookingId, 'Đã thanh toán');
                $this->bookingModel->updateStatus($bookingId, 'Đã xác nhận');
            }
        }
        
        echo json_encode(['message' => 'OK']);
        exit();
    }
}
?>