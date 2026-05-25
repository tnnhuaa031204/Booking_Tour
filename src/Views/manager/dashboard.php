<?php
// src/Views/manager/dashboard.php
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard Manager</h2>
    
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
                    <h5>Chờ duyệt</h5>
                    <h3><?= $pendingBookings ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Tour đang hoạt động</h5>
                    <h3><?= $activeTours ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
 ?>