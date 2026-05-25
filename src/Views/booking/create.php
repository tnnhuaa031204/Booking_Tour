<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Thêm tour mới</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/tours/store" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã tour *</label>
                    <input type="text" name="tour_code" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên tour *</label>
                    <input type="text" name="tour_name" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số ngày *</label>
                    <input type="number" name="duration" class="form-control" min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Mô tả chi tiết về tour..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Dịch vụ bao gồm</label>
                <textarea name="included_services" class="form-control" rows="3" placeholder="Liệt kê các dịch vụ bao gồm trong tour..."></textarea>
            </div>

            <!-- ====== UPLOAD ẢNH ====== -->
            <div class="mb-3">
                <label class="form-label fw-bold">Ảnh tour</label>
                <input type="file" name="images[]" id="imageInput" class="form-control" multiple accept="image/*">
                <div class="form-text">Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ là ảnh đại diện (thumbnail). Định dạng: JPG, PNG, WEBP, GIF.</div>
            </div>

            <!-- Preview ảnh trước khi upload -->
            <div id="previewContainer" class="row g-2 mb-3"></div>
            <!-- =========================== -->

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu tour</button>
            <a href="/admin/tours" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</a>
        </form>
    </div>
</div>

<script>
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
                        ${index === 0 ? '<span class="badge bg-success mt-1">Thumbnail</span>' : ''}
                    </div>
                </div>`;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>