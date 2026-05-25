<?php
// src/Views/sale/dashboard.php
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard Sale</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Tổng số booking của tôi</h5>
                    <h3><?= $myBookings ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Khách hàng đang chăm sóc</h5>
                    <h3><?= $myCustomers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Tour đang mở</h5>
                    <h3><?= $activeTours ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Doanh thu từ booking của tôi</h5>
                    <h3><?= number_format($myRevenue, 0, ',', '.') ?>đ</h3>
                </div>
            </div>
        </div>
    </div>
</div>