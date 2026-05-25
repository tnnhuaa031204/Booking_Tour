<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-search"></i> Danh sách tour</h2>
    
    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/tour/list" class="row g-3">
                <div class="col-md-3">
                    <label for="keyword" class="form-label">Tên tour</label>
                    <input type="text" class="form-control" id="keyword" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Nhập tên tour">
                </div>
                <div class="col-md-3">
                    <label for="province" class="form-label">Khu vực</label>
                    <select class="form-select" id="province" name="province">
                        <option value="">Tất cả</option>
                        <?php foreach ($provinces as $p): ?>
                        <option value="<?= $p['ProvinceName'] ?>" <?= $province == $p['ProvinceName'] ? 'selected' : '' ?>>
                            <?= $p['ProvinceName'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="min_price" class="form-label">Giá từ</label>
                    <input type="number" class="form-control" id="min_price" name="min_price" value="<?= $minPrice ?>" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label for="max_price" class="form-label">Giá đến</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" value="<?= $maxPrice ?>" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label for="duration" class="form-label">Số ngày</label>
                    <select class="form-select" id="duration" name="duration">
                        <option value="0">Tất cả</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                        <option value="<?= $i ?>" <?= $duration == $i ? 'selected' : '' ?>><?= $i ?> ngày</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="/tour/list" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Danh sách tour -->
    <div class="row">
        <?php if (empty($tours)): ?>
        <div class="col-12">
            <div class="alert alert-info">Không tìm thấy tour nào.</div>
        </div>
        <?php else: ?>
        <?php foreach ($tours as $tour): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <?php if (!empty($tour['ThumbnailURL'])): ?>
                <img src="<?= htmlspecialchars($tour['ThumbnailURL']) ?>" class="card-img-top" alt="<?= htmlspecialchars($tour['TourName']) ?>" style="height:200px;object-fit:cover;">
                <?php else: ?>
                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:200px;">
                    <i class="fas fa-image fa-3x text-muted"></i>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= $tour['TourName'] ?></h5>
                    <p class="card-text text-muted"><?= $tour['Duration'] ?> ngày</p>
                    <p class="card-text fw-bold text-danger"><?= number_format($tour['Price'] ?? 0, 0, ',', '.') ?>đ</p>
                    <a href="/tour/detail/<?= $tour['TourID'] ?>" class="btn btn-primary w-100">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>