<?php
// src/Controllers/HomeController.php

require_once 'BaseController.php';

class HomeController extends BaseController {
    
    public function index(): void {
        $db = db();
        $stmt = $db->query("
            SELECT TOP 6
                t.TourID, t.TourCode, t.TourName, t.Duration, t.Description,
                (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) as Price,
                (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL
            FROM Tours t
            WHERE t.IsActive = 1
            ORDER BY t.CreatedAt DESC
        ");
        $tours = $stmt->fetchAll();
        
        $this->view('home.index', ['tours' => $tours]);
    }
    
    public function about(): void {
        $this->view('home.about');
    }
    
    public function contact(): void {
        $this->view('home.contact');
    }
}
?>