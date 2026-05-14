<?php
// src/Controllers/CRMController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/CRMLog.php';
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Customer.php';

class CRMController extends BaseController {
    
    private CRMLog $crmLogModel;
    private Task $taskModel;
    private Customer $customerModel;
    
    public function __construct() {
        $this->crmLogModel = new CRMLog();
        $this->taskModel = new Task();
        $this->customerModel = new Customer();
    }
    
    // ====== HÀM INDEX (MỚI THÊM) ======
    public function index() {
        // Chuyển hướng về logs mặc định
        $this->redirect('/admin/crm/logs');
    }
    
    // ====== CRM Logs ======
    public function logs() {
        if (!$this->hasPermission('MANAGE_CRM', 'CanView')) {
            $_SESSION['error'] = 'Bạn không có quyền xem CRM';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $logs = $this->crmLogModel->getAll();
        $this->view('admin.crm.logs', ['logs' => $logs]);
    }
    
    public function createLog() {
        if (!$this->hasPermission('MANAGE_CRM', 'CanCreate')) {
            $_SESSION['error'] = 'Bạn không có quyền tạo ghi chú CRM';
            $this->redirect('/admin/crm');
            return;
        }
        
        if ($this->isPost()) {
            $customerId = $this->post('customer_id');
            $interactionType = $this->post('interaction_type');
            $content = $this->post('content');
            $employeeId = $_SESSION['user']['UserID'];
            
            $this->crmLogModel->create($customerId, $employeeId, $interactionType, $content);
            $_SESSION['success'] = 'Đã thêm ghi chú CRM!';
            $this->redirect('/admin/crm/logs');
        }
        
        $customers = $this->customerModel->getAll();
        $this->view('admin.crm.create_log', ['customers' => $customers]);
    }
    
    // ====== Tasks ======
    public function tasks() {
        if (!$this->hasPermission('MANAGE_CRM', 'CanView')) {
            $_SESSION['error'] = 'Bạn không có quyền xem tasks';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $tasks = $this->taskModel->getAll();
        $this->view('admin.crm.tasks', ['tasks' => $tasks]);
    }
    
    public function createTask() {
        if (!$this->hasPermission('MANAGE_CRM', 'CanCreate')) {
            $_SESSION['error'] = 'Bạn không có quyền tạo task';
            $this->redirect('/admin/crm');
            return;
        }
        
        if ($this->isPost()) {
            $customerId = $this->post('customer_id');
            $title = $this->post('title');
            $dueDate = $this->post('due_date');
            $employeeId = $_SESSION['user']['UserID'];
            
            $this->taskModel->create($employeeId, $customerId, $title, $dueDate);
            $_SESSION['success'] = 'Đã tạo task mới!';
            $this->redirect('/admin/crm/tasks');
        }
        
        $customers = $this->customerModel->getAll();
        $this->view('admin.crm.create_task', ['customers' => $customers]);
    }
    
    public function completeTask($id = null) {
        if (!$this->hasPermission('MANAGE_CRM', 'CanEdit')) {
            $_SESSION['error'] = 'Bạn không có quyền cập nhật task';
            $this->redirect('/admin/crm');
            return;
        }
        
        $this->taskModel->markCompleted($id);
        $_SESSION['success'] = 'Đã hoàn thành task!';
        $this->redirect('/admin/crm/tasks');
    }
}
?>