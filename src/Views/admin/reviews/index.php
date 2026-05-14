<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-star"></i> Quản lý Đánh giá</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tour</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Chưa có đánh giá nào</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= $review['ReviewID'] ?></td>
                        <td><?= $review['CustomerName'] ?></td>
                        <td><?= $review['TourName'] ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $review['Rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                            <?php endfor; ?>
                        </td>
                        <td><?= htmlspecialchars($review['Comment'] ?? '') ?></td>
                        <td>
                            <?php if ($review['IsVisible']): ?>
                                <span class="badge bg-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($review['CreatedAt'])) ?></td>
                        <td>
                            <?php if ($review['IsVisible']): ?>
                                <a href="/admin/reviews/hide/<?= $review['ReviewID'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-eye-slash"></i> Ẩn
                                </a>
                            <?php else: ?>
                                <a href="/admin/reviews/approve/<?= $review['ReviewID'] ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> Duyệt
                                </a>
                            <?php endif; ?>
                            <a href="/admin/reviews/delete/<?= $review['ReviewID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
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