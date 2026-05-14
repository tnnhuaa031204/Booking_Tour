<?php
// src/Controllers/TourController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Tour.php';

class TourController extends BaseController {
    
    private \Tour $tourModel;
    
    public function __construct() {
        $this->tourModel = new Tour();
    }
    
    // Hiển thị danh sách tour
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;
        
        $tours = $this->tourModel->getAll($limit, $offset);
        $total = $this->tourModel->getTotal();
        $totalPages = ceil($total / $limit);
        
        $this->view('tour.list', [
            'tours' => $tours,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    // Hiển thị chi tiết tour
    public function detail($id = null) {
        if ($id === null) {
            $this->redirect('/tour');
            return;
        }
        
        $tour = $this->tourModel->getById($id);
        
        if (!$tour) {
            http_response_code(404);
            echo "404 - Không tìm thấy tour";
            return;
        }
        
        $schedules = $this->tourModel->getSchedules($id);
        $images = $this->tourModel->getImages($id);
        
        $this->view('tour.detail', [
            'tour' => $tour,
            'schedules' => $schedules,
            'images' => $images
        ]);
    }
}
?>