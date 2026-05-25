<?php
// src/Controllers/InvoiceController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Services/EmailService.php';

class InvoiceController extends BaseController {
    
    private Invoice $invoiceModel;
    private Booking $bookingModel;
    private EmailService $emailService;
    
    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->bookingModel = new Booking();
        $this->emailService = new EmailService();
    }
    
    // Danh sách hóa đơn
    public function index() {
        if (!$this->hasPermission('INVOICE_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $invoices = $this->invoiceModel->getAll();
        $this->view('admin.invoices.index', ['invoices' => $invoices]);
    }
    
    // Xem chi tiết hóa đơn
    public function detail($id = null) {
        if (!$this->hasPermission('INVOICE_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        $invoice = $this->invoiceModel->getById($id);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        $this->view('admin.invoices.detail', ['invoice' => $invoice]);
    }
    
    // Tạo hóa đơn mới từ booking
    public function create($bookingId = null) {
        if (!$this->hasPermission('INVOICE_CREATE', 'CanCreate')) {
            $_SESSION['error'] = 'Bạn không có quyền tạo hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        if ($this->isPost()) {
            $bookingId = $this->post('booking_id');
            $invoiceNumber = 'INV' . date('YmdHis') . rand(100, 999);
            
            $result = $this->invoiceModel->create($invoiceNumber, $bookingId);
            
            if ($result) {
                $_SESSION['success'] = 'Tạo hóa đơn thành công!';
                $this->redirect('/admin/invoices');
            } else {
                $_SESSION['error'] = 'Tạo hóa đơn thất bại!';
            }
        }
        
        $bookings = $this->bookingModel->getByStatus('Đã xác nhận');
        $this->view('admin.invoices.create', ['bookings' => $bookings]);
    }
    
    // Gửi email hóa đơn
    public function sendEmail($id = null) {
        if (!$this->hasPermission('INVOICE_VIEW', 'CanExport')) {
            $_SESSION['error'] = 'Bạn không có quyền gửi email hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        $invoice = $this->invoiceModel->getById($id);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Gửi email
        $customerEmail = $invoice['CustomerEmail'] ?? '';
        $this->emailService->sendInvoiceEmail($invoice, $customerEmail);
        
        $this->invoiceModel->markAsSent($id);
        $_SESSION['success'] = 'Đã gửi email hóa đơn!';
        $this->redirect('/admin/invoices');
    }
    
    // Xuất PDF (placeholder)
    public function pdf($id = null) {
        if (!$this->hasPermission('INVOICE_VIEW')) {
            $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Code xuất PDF sẽ thêm sau
        $_SESSION['info'] = 'Tính năng xuất PDF đang phát triển';
        $this->redirect('/admin/invoices/detail/' . $id);
    }
}
?>