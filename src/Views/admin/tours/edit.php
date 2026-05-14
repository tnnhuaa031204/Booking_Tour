<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-edit"></i> Sửa tour</h5>
    </div>
    <div class="card-body">
        <?php if (isset($tour) && $tour): ?>
        <form method="POST" action="/admin/tours/update/<?= $tour['TourID'] ?>">
            <div class="mb-3">
                <label class="form-label">Mã tour *</label>
                <input type="text" name="tour_code" class="form-control" value="<?= htmlspecialchars($tour['TourCode']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên tour *</label>
                <input type="text" name="tour_name" class="form-control" value="<?= htmlspecialchars($tour['TourName']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số ngày *</label>
                <input type="number" name="duration" class="form-control" value="<?= $tour['Duration'] ?>" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($tour['Description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Dịch vụ bao gồm</label>
                <textarea name="included_services" class="form-control" rows="3"><?= htmlspecialchars($tour['IncludedServices'] ?? '') ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" value="1" <?= $tour['IsActive'] ? 'checked' : '' ?>>
                <label class="form-check-label">Hoạt động</label>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="/admin/tours" class="btn btn-secondary">Hủy</a>
        </form>
        <?php else: ?>
            <div class="alert alert-danger">Không tìm thấy thông tin tour</div>
            <a href="/admin/tours" class="btn btn-primary">Quay lại</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>