<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-star"></i> Chi tiết đánh giá</h2>
    <a href="/admin/reviews" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($review): ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <td><?= $review['ReviewID'] ?></td>
                    </tr>
                    <tr>
                        <th>Khách hàng</th>
                        <td><?= $review['CustomerName'] ?></td>
                    </tr>
                    <tr>
                        <th>Tour</th>
                        <td><?= $review['TourName'] ?></td>
                    </tr>
                    <tr>
                        <th>Đánh giá</th>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $review['Rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                            <?php endfor; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($review['IsVisible']): ?>
                                <span class="badge bg-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= date('d/m/Y H:i', strtotime($review['CreatedAt'])) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <strong>Nội dung</strong>
                    </div>
                    <div class="card-body">
                        <?= nl2br(htmlspecialchars($review['Comment'] ?? 'Không có nội dung')) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <?php if ($review['IsVisible']): ?>
                <a href="/admin/reviews/hide/<?= $review['ReviewID'] ?>" class="btn btn-warning">
                    <i class="fas fa-eye-slash"></i> Ẩn
                </a>
            <?php else: ?>
                <a href="/admin/reviews/approve/<?= $review['ReviewID'] ?>" class="btn btn-success">
                    <i class="fas fa-eye"></i> Duyệt
                </a>
            <?php endif; ?>
            <a href="/admin/reviews/delete/<?= $review['ReviewID'] ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                <i class="fas fa-trash"></i> Xóa
            </a>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">Không tìm thấy đánh giá</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>