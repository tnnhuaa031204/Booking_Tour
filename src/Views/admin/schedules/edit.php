<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-edit"></i> Sửa lịch khởi hành</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/schedule/update/<?= $schedule['ScheduleID'] ?>">
            <div class="mb-3">
                <label class="form-label">Chọn tour *</label>
                <select name="tour_id" class="form-control" required>
                    <?php foreach ($tours as $tour): ?>
                        <option value="<?= $tour['TourID'] ?>" <?= $tour['TourID'] == $schedule['TourID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tour['TourName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày khởi hành *</label>
                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime($schedule['StartDate'])) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc *</label>
                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime($schedule['EndDate'])) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá *</label>
                    <input type="number" name="price" class="form-control" value="<?= $schedule['Price'] ?>" min="0" step="1000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tổng số chỗ *</label>
                    <input type="number" name="total_slots" class="form-control" value="<?= $schedule['TotalSlots'] ?>" min="1" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="Đang mở" <?= $schedule['Status'] == 'Đang mở' ? 'selected' : '' ?>>Đang mở</option>
                    <option value="Đã kín" <?= $schedule['Status'] == 'Đã kín' ? 'selected' : '' ?>>Đã kín</option>
                    <option value="Đã khởi hành" <?= $schedule['Status'] == 'Đã khởi hành' ? 'selected' : '' ?>>Đã khởi hành</option>
                    <option value="Hủy" <?= $schedule['Status'] == 'Hủy' ? 'selected' : '' ?>>Hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="/admin/schedules" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>