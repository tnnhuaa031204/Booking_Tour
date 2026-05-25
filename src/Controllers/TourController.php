<?php
// src/Controllers/TourController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Tour.php';

class TourController extends BaseController {
    
    private Tour $tourModel;
    
    public function __construct() {
        $this->tourModel = new Tour();
    }
    
    public function list() {
        $keyword = $this->get('keyword', '');
        $province = $this->get('province', '');
        $minPrice = (float)$this->get('min_price', 0);
        $maxPrice = (float)$this->get('max_price', 0);
        $duration = (int)$this->get('duration', 0);
        $page = (int)$this->get('page', 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $tours = $this->tourModel->searchWithFilters($keyword, $province, $minPrice, $maxPrice, $duration, $limit, $offset);
        $total = $this->tourModel->getTotal();
        
        // Lấy danh sách tỉnh thành để hiển thị bộ lọc
        $db = db();
        $stmt = $db->query("SELECT ProvinceID, ProvinceName FROM Provinces WHERE IsActive = 1 ORDER BY ProvinceName");
        $provinces = $stmt->fetchAll();
        
        $this->view('tour.list', [
            'tours' => $tours,
            'keyword' => $keyword,
            'province' => $province,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'duration' => $duration,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
            'provinces' => $provinces
        ]);
    }
    
    public function detail($id = null) {
        if (!$id) {
            $this->redirect('/tour/list');
            return;
        }
        
        $tour = $this->tourModel->getById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour';
            $this->redirect('/tour/list');
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