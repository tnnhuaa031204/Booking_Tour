<?php
// src/Controllers/ManagerController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Tour.php';

class ManagerController extends BaseController {
    
    public function dashboard() {
        // Kiểm tra role
        if (!$this->hasRole('Manager')) {
            $this->redirect('/');
            return;
        }
        
        $bookingModel = new Booking();
        $tourModel = new Tour();
        
        $totalRevenue = $bookingModel->getTotalRevenue();
        $totalBookings = $bookingModel->getTotalBookings();
        $pendingBookings = $bookingModel->getPendingBookings();
        $activeTours = $tourModel->getActiveToursCount();
        
        $this->view('manager.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'activeTours' => $activeTours
        ]);
    }
}