<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12 text-center mb-4">
        <h2>🌟 Tour nổi bật</h2>
        <p>Những hành trình được yêu thích nhất</p>
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
            <div class="card tour-card h-100 shadow-sm">
                <!-- Ảnh thumbnail -->
                <?php if (!empty($tour['ThumbnailURL'])): ?>
                    <img src="<?= htmlspecialchars($tour['ThumbnailURL']) ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($tour['TourName']) ?>"
                         style="height:200px; object-fit:cover;">
                <?php else: ?>
                    <div style="height:200px; background:#e9ecef; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><?= htmlspecialchars($tour['TourName']) ?></h5>
                    <p class="card-text">
                        <i class="fas fa-tag"></i> <strong>Mã tour:</strong> <?= htmlspecialchars($tour['TourCode']) ?><br>
                        <i class="fas fa-clock"></i> <strong>Thời gian:</strong> <?= $tour['Duration'] ?> ngày
                    </p>
                    <p class="card-text text-muted small flex-grow-1">
                        <?= htmlspecialchars(substr($tour['Description'] ?? '', 0, 100)) ?>...
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="tour-price"><?= number_format(($tour['Price'] ?? 2500000), 0, ',', '.') ?>đ</span>
                        <a href="/tour/detail/<?= $tour['TourID'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-info-circle"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>