<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-edit"></i> Sửa tour</h5>
    </div>
    <div class="card-body">
        <?php if (isset($tour) && $tour): ?>
        <form method="POST" action="/admin/tours/update/<?= $tour['TourID'] ?>" enctype="multipart/form-data">

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

            <!-- ====== ẢNH HIỆN TẠI ====== -->
            <?php if (!empty($images)): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Ảnh hiện tại</label>
                <div class="row g-2">
                    <?php foreach ($images as $img): ?>
                    <div class="col-6 col-md-3" id="img-<?= $img['ImageID'] ?>">
                        <div class="card h-100 <?= $img['IsThumbnail'] ? 'border-success border-2' : '' ?>">
                            <img src="<?= htmlspecialchars($img['ImageURL']) ?>"
                                 class="card-img-top"
                                 style="height:140px;object-fit:cover;">
                            <div class="card-body p-2">
                                <?php if ($img['IsThumbnail']): ?>
                                    <span class="badge bg-success mb-1">Thumbnail</span>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-success btn-sm mb-1 w-100"
                                            onclick="setThumbnail(<?= $img['ImageID'] ?>)">
                                        <i class="fas fa-star"></i> Đặt làm thumbnail
                                    </button>
                                <?php endif; ?>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox"
                                           name="delete_images[]"
                                           value="<?= $img['ImageID'] ?>"
                                           id="del_<?= $img['ImageID'] ?>"
                                           onchange="markDelete(this, <?= $img['ImageID'] ?>)">
                                    <label class="form-check-label text-danger small" for="del_<?= $img['ImageID'] ?>">Xóa ảnh này</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Input ẩn để set thumbnail -->
            <input type="hidden" name="set_thumbnail" id="setThumbnailInput" value="">
            <!-- ============================ -->

            <!-- ====== UPLOAD ẢNH MỚI ====== -->
            <div class="mb-3">
                <label class="form-label fw-bold">Thêm ảnh mới</label>
                <input type="file" name="images[]" id="imageInput" class="form-control" multiple accept="image/*">
                <div class="form-text">Định dạng: JPG, PNG, WEBP, GIF. Có thể chọn nhiều ảnh cùng lúc.</div>
            </div>

            <!-- Preview ảnh mới -->
            <div id="previewContainer" class="row g-2 mb-3"></div>
            <!-- ================================ -->

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
            <a href="/admin/tours" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</a>
        </form>

        <?php else: ?>
            <div class="alert alert-danger">Không tìm thấy thông tin tour</div>
            <a href="/admin/tours" class="btn btn-primary">Quay lại</a>
        <?php endif; ?>
    </div>
</div>

<script>
// Preview ảnh mới chọn
document.getElementById('imageInput').addEventListener('change', function () {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3';
            col.innerHTML = `
                <div class="card h-100">
                    <img src="${e.target.result}" class="card-img-top" style="height:140px;object-fit:cover;">
                    <div class="card-body p-2">
                        <input type="text" name="captions[]" class="form-control form-control-sm" placeholder="Chú thích (tùy chọn)">
                    </div>
                </div>`;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});

// Đặt thumbnail
function setThumbnail(imageId) {
    document.getElementById('setThumbnailInput').value = imageId;
    // Cập nhật UI badge
    document.querySelectorAll('.badge.bg-success').forEach(b => b.remove());
    const card = document.querySelector(`#img-${imageId} .card`);
    if (card) {
        card.classList.add('border-success', 'border-2');
        card.querySelector('.card-body').insertAdjacentHTML('afterbegin', '<span class="badge bg-success mb-1">Thumbnail</span>');
    }
}

// Highlight ảnh sắp bị xóa
function markDelete(checkbox, imageId) {
    const card = document.querySelector(`#img-${imageId} .card`);
    if (checkbox.checked) {
        card.classList.add('opacity-50', 'border-danger', 'border-2');
    } else {
        card.classList.remove('opacity-50', 'border-danger', 'border-2');
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>