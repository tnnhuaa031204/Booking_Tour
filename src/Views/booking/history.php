<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-history"></i> Lịch sử đặt tour</h4>
                <a href="/review/my-reviews" class="btn btn-light btn-sm">
                    <i class="fas fa-star text-warning"></i> Đánh giá của tôi
                </a>
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
                        <table class="table table-bordered table-striped align-middle">
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                <?php
                                $canCancel   = (strpos($b['PaymentStatus'], 'Chưa') !== false && strpos($b['BookingStatus'], 'Chờ') !== false);
                                $isCompleted = stripos($b['BookingStatus'], 'Hoàn thành') !== false || $b['BookingStatus'] === 'Hoàn thành';

                                // Kiểm tra đã đánh giá chưa (truyền từ controller)
                                $hasReviewed = !empty($b['HasReviewed']);
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
                                            echo '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
                                        } elseif (stripos($b['BookingStatus'], 'Hoàn thành') !== false) {
                                            echo '<span class="badge bg-primary">Hoàn thành</span>';
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
                                        <?php if (strpos($b['PaymentStatus'], 'Chưa') !== false): ?>
                                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <?php if ($canCancel): ?>
                                                <a href="/booking/cancel/<?= $b['BookingID'] ?>"
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Bạn có chắc muốn hủy tour này?')">
                                                    <i class="fas fa-times"></i> Hủy tour
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($isCompleted): ?>
                                                <?php if ($hasReviewed): ?>
                                                    <span class="badge bg-success p-2">
                                                        <i class="fas fa-check"></i> Đã đánh giá
                                                    </span>
                                                <?php else: ?>
                                                    <a href="/review/create/<?= $b['BookingID'] ?>"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-star"></i> Đánh giá
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if (!$canCancel && !$isCompleted): ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </div>
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