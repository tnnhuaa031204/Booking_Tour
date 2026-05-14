<?php
// src/Controllers/HomeController.php

require_once 'BaseController.php';

class HomeController extends BaseController {
    
    public function index(): void {
        $db = db();
        $stmt = $db->query("SELECT TOP 6 TourID, TourCode, TourName, Duration FROM Tours WHERE IsActive = 1 ORDER BY CreatedAt DESC");
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