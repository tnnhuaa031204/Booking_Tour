<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Thêm lịch khởi hành</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/schedule/store">
            <div class="mb-3">
                <label class="form-label">Chọn tour *</label>
                <select name="tour_id" class="form-control" required>
                    <option value="">-- Chọn tour --</option>
                    <?php foreach ($tours as $tour): ?>
                        <option value="<?= $tour['TourID'] ?>"><?= htmlspecialchars($tour['TourName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày khởi hành *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc *</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá *</label>
                    <input type="number" name="price" class="form-control" min="0" step="1000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tổng số chỗ *</label>
                    <input type="number" name="total_slots" class="form-control" min="1" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="/admin/schedules" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>