<?php
// src/Views/accountant/dashboard.php
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard Accountant</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Tổng doanh thu</h5>
                    <h3><?= number_format($totalRevenue, 0, ',', '.') ?>đ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Tổng đơn đặt</h5>
                    <h3><?= $totalBookings ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Chưa thanh toán</h5>
                    <h3><?= $pendingPayments ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Công nợ</h5>
                    <h3><?= number_format($debt, 0, ',', '.') ?>đ</h3>
                </div>
            </div>
        </div>
    </div>
</div>