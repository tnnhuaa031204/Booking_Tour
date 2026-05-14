<?php
/** @var array $tours */
/** @var int $total */
/** @var int $currentPage */
/** @var int $totalPages */
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2>📍 Danh sách tour du lịch</h2>
        <p class="text-muted">Tổng cộng <strong><?= $total ?? 0 ?></strong> tour</p>
        <hr>
    </div>
</div>

<div class="row">
    <?php if (empty($tours)): ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle"></i> Chưa có tour nào. Vui lòng thêm tour vào database.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($tours as $tour): ?>
        <div class="col-md-4 mb-4">
            <div class="card tour-card h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary"><?= htmlspecialchars($tour['TourName']) ?></h5>
                    <p class="card-text">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($tour['TourCode']) ?><br>
                        <i class="fas fa-clock"></i> <?= $tour['Duration'] ?> ngày
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="tour-price"><?= number_format(($tour['Price'] ?? 2500000), 0, ',', '.') ?>đ</span>
                        <a href="/tour/detail/<?= $tour['TourID'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-info-circle"></i> Chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Phân trang -->
<?php if ($totalPages > 1): ?>
<div class="row mt-4">
    <div class="col-12">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="/tour?page=<?= $currentPage - 1 ?>">« Trước</a></li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="/tour?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="/tour?page=<?= $currentPage + 1 ?>">Sau »</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>