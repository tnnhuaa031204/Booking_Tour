<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-info-circle"></i> Chi tiết booking: <?= $booking['BookingCode'] ?></h5>
        <?php
        $statusClass = match($booking['BookingStatus']) {
            'Chờ xác nhận' => 'warning',
            'Đã xác nhận'  => 'success',
            'Hoàn thành'   => 'info',
            'Đã hủy'       => 'danger',
            default        => 'secondary'
        };
        ?>
        <span class="badge bg-<?= $statusClass ?> fs-6"><?= $booking['BookingStatus'] ?></span>
    </div>
    <div class="card-body">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin khách hàng</h6>
                <table class="table table-bordered">
                    <tr><th>Họ tên</th><td><?= htmlspecialchars($booking['CustomerName']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($booking['CustomerEmail']) ?></td></tr>
                    <tr><th>Số điện thoại</th><td><?= htmlspecialchars($booking['CustomerPhone']) ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Thông tin tour</h6>
                <table class="table table-bordered">
                    <tr><th>Tên tour</th><td><?= htmlspecialchars($booking['TourName']) ?></td></tr>
                    <tr><th>Mã tour</th><td><?= htmlspecialchars($booking['TourCode']) ?></td></tr>
                    <tr><th>Ngày khởi hành</th><td><?= date('d/m/Y', strtotime($booking['StartDate'])) ?></td></tr>
                    <tr><th>Ngày kết thúc</th><td><?= date('d/m/Y', strtotime($booking['EndDate'])) ?></td></tr>
                    <tr><th>Số lượng</th><td>Người lớn: <?= $booking['AdultCount'] ?>, Trẻ em: <?= $booking['ChildCount'] ?></td></tr>
                    <tr><th>Tổng tiền</th><td class="text-danger fw-bold"><?= number_format($booking['TotalAmount'], 0, ',', '.') ?>đ</td></tr>
                    <tr>
                        <th>Thanh toán</th>
                        <td>
                            <?php if ($booking['PaymentStatus'] == 'Đã thanh toán'): ?>
                                <span class="badge bg-success">Đã thanh toán</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <h6 class="mt-3">Danh sách khách đi tour</h6>
        <table class="table table-bordered">
            <thead><tr><th>STT</th><th>Họ tên</th><th>Loại</th></tr></thead>
            <tbody>
                <?php foreach ($passengers as $k => $p): ?>
                <tr>
                    <td><?= $k+1 ?></td>
                    <td><?= htmlspecialchars($p['FullName']) ?></td>
                    <td><?= $p['PassengerType'] == 'Adult' ? 'Người lớn' : 'Trẻ em' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Các nút thao tác theo trạng thái -->
        <div class="mt-4 d-flex gap-2 flex-wrap">

            <?php if ($booking['BookingStatus'] == 'Chờ xác nhận'): ?>
                <a href="/admin/bookings/confirm/<?= $booking['BookingID'] ?>"
                   class="btn btn-success"
                   onclick="return confirm('Xác nhận booking này?')">
                    <i class="fas fa-check"></i> Xác nhận booking
                </a>
                <a href="/admin/bookings/cancel/<?= $booking['BookingID'] ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Hủy booking này?')">
                    <i class="fas fa-times"></i> Hủy booking
                </a>
            <?php endif; ?>

            <?php if ($booking['PaymentStatus'] != 'Đã thanh toán'): ?>
                <a href="/admin/bookings/payment-confirm/<?= $booking['BookingID'] ?>"
                   class="btn btn-primary"
                   onclick="return confirm('Xác nhận đã thanh toán?')">
                    <i class="fas fa-money-bill-wave"></i> Xác nhận thanh toán
                </a>
            <?php endif; ?>

            <?php if ($booking['BookingStatus'] == 'Đã xác nhận'): ?>
                <a href="/admin/bookings/complete/<?= $booking['BookingID'] ?>"
                   class="btn btn-info text-white"
                   onclick="return confirm('Đánh dấu tour này đã hoàn thành?\nKhách hàng sẽ có thể gửi đánh giá.')">
                    <i class="fas fa-flag-checkered"></i> Đánh dấu Hoàn thành
                </a>
            <?php endif; ?>

            <?php if ($booking['BookingStatus'] == 'Hoàn thành'): ?>
                <span class="badge bg-info fs-6 p-2">
                    <i class="fas fa-flag-checkered"></i> Tour đã hoàn thành — Khách có thể đánh giá
                </span>
            <?php endif; ?>

            <a href="/admin/bookings" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>