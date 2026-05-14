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
        $this->tourModel = new Tour();
    }
    
    // Danh sách đánh giá
    public function index() {
        if (!$this->hasPermission('MANAGE_REVIEWS', 'CanView')) {
            $_SESSION['error'] = 'Bạn không có quyền xem đánh giá';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $reviews = $this->reviewModel->getAll();
        $this->view('admin.reviews.index', ['reviews' => $reviews]);
    }
    
    // Duyệt đánh giá (hiển thị công khai)
    public function approve($id = null) {
        if (!$this->hasPermission('MANAGE_REVIEWS', 'CanApprove')) {
            $_SESSION['error'] = 'Bạn không có quyền duyệt đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $this->reviewModel->updateVisibility($id, 1);
        $_SESSION['success'] = 'Đã duyệt đánh giá!';
        $this->redirect('/admin/reviews');
    }
    
    // Ẩn đánh giá
    public function hide($id = null) {
        if (!$this->hasPermission('MANAGE_REVIEWS', 'CanApprove')) {
            $_SESSION['error'] = 'Bạn không có quyền ẩn đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $this->reviewModel->updateVisibility($id, 0);
        $_SESSION['success'] = 'Đã ẩn đánh giá!';
        $this->redirect('/admin/reviews');
    }
    
    // Xóa đánh giá
    public function delete($id = null) {
        if (!$this->hasPermission('MANAGE_REVIEWS', 'CanDelete')) {
            $_SESSION['error'] = 'Bạn không có quyền xóa đánh giá';
            $this->redirect('/admin/reviews');
            return;
        }
        
        $this->reviewModel->delete($id);
        $_SESSION['success'] = 'Đã xóa đánh giá!';
        $this->redirect('/admin/reviews');
    }
}
?>