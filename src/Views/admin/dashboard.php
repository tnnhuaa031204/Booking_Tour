<?php require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Tổng quan</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-number"><?= $totalTours ?? 0 ?></span>
                    <p class="mb-0 text-muted">Tổng số tour</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-number"><?= $totalBookings ?? 0 ?></span>
                    <p class="mb-0 text-muted">Tổng đơn đặt</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-number"><?= $totalCustomers ?? 0 ?></span>
                    <p class="mb-0 text-muted">Khách hàng</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-number"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?>đ</span>
                    <p class="mb-0 text-muted">Doanh thu</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>