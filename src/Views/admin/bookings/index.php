<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-ticket-alt"></i> Quản lý Booking</h2>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã booking</th>
                        <th>Khách hàng</th>
                        <th>Tour</th>
                        <th>Ngày khởi hành</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="9" class="text-center">Chưa có booking nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $item): ?>
                        <tr>
                            <td><?= $item['BookingID'] ?></td>
                            <td><?= $item['BookingCode'] ?></td>
                            <td><?= htmlspecialchars($item['CustomerName']) ?><br><small><?= $item['CustomerPhone'] ?></small></td>
                            <td><?= htmlspecialchars($item['TourName']) ?> (<small><?= $item['TourCode'] ?></small>)</td>
                            <td><?= date('d/m/Y', strtotime($item['StartDate'])) ?></td>
                            <td><?= number_format($item['TotalAmount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php
                                $statusClass = match($item['BookingStatus']) {
                                    'Chờ xác nhận' => 'warning',
                                    'Đã xác nhận' => 'success',
                                    'Đã hủy' => 'danger',
                                    'Hoàn thành' => 'info',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $item['BookingStatus'] ?></span>
                            </td>
                            <td>
                                <?php
                                $paymentClass = $item['PaymentStatus'] == 'Đã thanh toán' ? 'success' : 'warning';
                                ?>
                                <span class="badge bg-<?= $paymentClass ?>"><?= $item['PaymentStatus'] ?></span>
                            </td>
                            <td>
                                <a href="/admin/bookings/detail/<?= $item['BookingID'] ?>" class="btn btn-sm btn-info">Chi tiết</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>