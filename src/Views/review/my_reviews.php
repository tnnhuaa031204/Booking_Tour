<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h4 class="mb-4"><i class="fas fa-star text-warning"></i> Đánh giá của tôi</h4>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
        <div class="alert alert-info">Bạn chưa có đánh giá nào.</div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($reviews as $review): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-primary"><?= htmlspecialchars($review['TourName']) ?></h6>
                            <small class="text-muted">Mã booking: <?= htmlspecialchars($review['BookingCode']) ?></small>
                        </div>
                        <div class="text-end">
                            <!-- Hiện sao -->
                            <div class="text-warning fs-5">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $review['Rating'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($review['CreatedAt'])) ?></small>
                        </div>
                    </div>

                    <?php if (!empty($review['Comment'])): ?>
                    <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($review['Comment'])) ?></p>
                    <?php endif; ?>

                    <div class="mt-2">
                        <?php if ($review['IsVisible']): ?>
                            <span class="badge bg-success"><i class="fas fa-check"></i> Đã duyệt</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><i class="fas fa-clock"></i> Chờ duyệt</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="/booking/history" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại lịch sử booking
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>