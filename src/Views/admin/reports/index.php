<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-bar"></i> Báo cáo thống kê</h2>
        <hr>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Tổng doanh thu</h5>
                <p class="card-text display-6"><?= number_format($summary['total_revenue'], 0, ',', '.') ?>đ</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Tổng đơn đặt</h5>
                <p class="card-text display-6"><?= $summary['total_bookings'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Khách hàng</h5>
                <p class="card-text display-6"><?= $summary['total_customers'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Tour đang hoạt động</h5>
                <p class="card-text display-6"><?= $summary['total_tours'] ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Doanh thu theo tháng -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Doanh thu theo tháng năm <?= $currentYear ?></h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
                <div class="mt-3">
                    <a href="/admin/reports/export-revenue?year=<?= $currentYear ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top tour bán chạy -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-trophy"></i> Top 10 tour bán chạy nhất</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>STT</th><th>Mã tour</th><th>Tên tour</th><th>Số lượng đặt</th><th>Doanh thu</th></thead>
                    <tbody>
                        <?php foreach ($topTours as $k => $tour): ?>
                        <tr>
                            <td><?= $k+1 ?></td>
                            <td><?= htmlspecialchars($tour['TourCode']) ?></td>
                            <td><?= htmlspecialchars($tour['TourName']) ?></td>
                            <td><?= $tour['total_bookings'] ?></td>
                            <td><?= number_format($tour['total_revenue'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dữ liệu doanh thu theo tháng
const monthlyData = <?= json_encode(array_values($monthlyRevenue)) ?>;

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: monthlyData,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + 'đ';
                    }
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>