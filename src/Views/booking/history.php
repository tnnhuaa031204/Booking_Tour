<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-history"></i> Lịch sử đặt tour</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info">Bạn chưa có đơn đặt tour nào.</div>
                    <a href="/tour" class="btn btn-primary">Đặt tour ngay</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mã booking</th>
                                    <th>Tour</th>
                                    <th>Ngày khởi hành</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Thao tác</th>
                                </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                <?php
                                // Dùng strpos để kiểm tra (không phụ thuộc lỗi font)
                                $canCancel = (strpos($b['PaymentStatus'], 'Chưa') !== false && strpos($b['BookingStatus'], 'Chờ') !== false);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['BookingCode']) ?></td>
                                    <td><?= htmlspecialchars($b['TourName']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($b['StartDate'])) ?></td>
                                    <td>NL: <?= $b['AdultCount'] ?> | TE: <?= $b['ChildCount'] ?></td>
                                    <td><?= number_format($b['TotalAmount'], 0, ',', '.') ?>đ</td>
                                    <td>
                                        <?php
                                        if (strpos($b['BookingStatus'], 'Chờ') !== false) {
                                            echo '<span class="badge bg-warning">Chờ xác nhận</span>';
                                        } elseif (strpos($b['BookingStatus'], 'xác nhận') !== false) {
                                            echo '<span class="badge bg-success">Đã xác nhận</span>';
                                        } elseif (strpos($b['BookingStatus'], 'hủy') !== false) {
                                            echo '<span class="badge bg-danger">Đã hủy</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">' . htmlspecialchars($b['BookingStatus']) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (strpos($b['PaymentStatus'], 'Chưa') !== false) {
                                            echo '<span class="badge bg-warning">Chưa thanh toán</span>';
                                        } else {
                                            echo '<span class="badge bg-success">Đã thanh toán</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($canCancel): ?>
                                            <a href="/booking/cancel/<?= $b['BookingID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn hủy tour này?')">Hủy tour</a>
                                        <?php else: ?>
                                            <span class="text-muted">Không thể hủy</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>