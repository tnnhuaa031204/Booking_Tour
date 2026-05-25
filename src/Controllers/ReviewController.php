<?php
// src/Controllers/ReviewController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Review.php';
require_once __DIR__ . '/../Models/Tour.php';

class ReviewController extends BaseController {

    private Review $reviewModel;
    private Tour $tourModel;

    public function __construct() {
        $this->reviewModel = new Review();
        $this->tourModel   = new Tour();
    }

    // ============================================================
    // ADMIN
    // ============================================================

    public function index() {
        if (!$this->hasPermission('REVIEW_VIEW') && !$this->hasRole(['Admin', 'Manager', 'Sale'])) {
            $_SESSION['error'] = 'Bạn không có quyền xem đánh giá';
            $this->redirect('/admin/dashboard');
            return;
        }
        $reviews = $this->reviewModel->getAll();
        $this->view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function approve($id = null) {
        if (!$this->hasPermission('REVIEW_HIDE', 'CanApprove') && !$this->hasRole('Sale')) {
            $_SESSION['error'] = 'Bạn không có quyền duyệt đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        $this->reviewModel->updateVisibility($id, 1);
        $_SESSION['success'] = 'Đã duyệt đánh giá!';
        $this->redirect('/admin/reviews');
    }

    public function hide($id = null) {
        if (!$this->hasPermission('REVIEW_HIDE', 'CanApprove') && !$this->hasRole('Sale')) {
            $_SESSION['error'] = 'Bạn không có quyền ẩn đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        $this->reviewModel->updateVisibility($id, 0);
        $_SESSION['success'] = 'Đã ẩn đánh giá!';
        $this->redirect('/admin/reviews');
    }

    public function delete($id = null) {
        if (!$this->hasPermission('REVIEW_VIEW', 'CanDelete') && !$this->hasRole('Sale')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        $this->reviewModel->delete($id);
        $_SESSION['success'] = 'Đã xóa đánh giá!';
        $this->redirect('/admin/reviews');
    }

    // ============================================================
    // CUSTOMER
    // ============================================================

    // Trang viết đánh giá cho 1 booking
    public function create($bookingId = null) {
        if (!$this->isLoggedIn() || !$this->hasRole('Customer')) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đánh giá';
            $this->redirect('/auth/login');
            return;
        }

        if (!$bookingId) {
            $this->redirect('/booking/history');
            return;
        }

        $userId   = $_SESSION['user']['UserID'];
        $booking  = $this->reviewModel->getBookingForReview($bookingId, $userId);

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking hoặc bạn không có quyền đánh giá';
            $this->redirect('/booking/history');
            return;
        }

        // Kiểm tra tour đã kết thúc chưa
        if ($booking['BookingStatus'] !== 'Hoàn thành') {
            $_SESSION['error'] = 'Bạn chỉ có thể đánh giá sau khi tour kết thúc';
            $this->redirect('/booking/history');
            return;
        }

        // Kiểm tra đã đánh giá chưa
        if ($this->reviewModel->hasReviewed($bookingId)) {
            $_SESSION['error'] = 'Bạn đã đánh giá booking này rồi';
            $this->redirect('/review/my-reviews');
            return;
        }

        $this->view('review.create', ['booking' => $booking]);
    }

    // Lưu đánh giá
    public function store() {
        if (!$this->isLoggedIn() || !$this->hasRole('Customer')) {
            $this->redirect('/auth/login');
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('/booking/history');
            return;
        }

        $bookingId = (int)$this->post('booking_id');
        $rating    = (int)$this->post('rating');
        $comment   = trim($this->post('comment', ''));
        $userId    = $_SESSION['user']['UserID'];

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng chọn số sao (1-5)';
            $this->redirect('/review/create/' . $bookingId);
            return;
        }

        $booking = $this->reviewModel->getBookingForReview($bookingId, $userId);
        if (!$booking || $booking['BookingStatus'] !== 'Hoàn thành') {
            $_SESSION['error'] = 'Không thể đánh giá booking này';
            $this->redirect('/booking/history');
            return;
        }

        if ($this->reviewModel->hasReviewed($bookingId)) {
            $_SESSION['error'] = 'Bạn đã đánh giá booking này rồi';
            $this->redirect('/review/my-reviews');
            return;
        }

        $result = $this->reviewModel->create(
            $bookingId,
            $booking['CustomerID'],
            $booking['TourID'],
            $rating,
            $comment
        );

        if ($result) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá!';
            $this->redirect('/review/my-reviews');
        } else {
            $_SESSION['error'] = 'Gửi đánh giá thất bại, vui lòng thử lại';
            $this->redirect('/review/create/' . $bookingId);
        }
    }

    // Danh sách đánh giá của customer
    public function myReviews() {
        if (!$this->isLoggedIn() || !$this->hasRole('Customer')) {
            $this->redirect('/auth/login');
            return;
        }

        $userId  = $_SESSION['user']['UserID'];
        $reviews = $this->reviewModel->getByUserId($userId);
        $this->view('review.my_reviews', ['reviews' => $reviews]);
    }
}
?>