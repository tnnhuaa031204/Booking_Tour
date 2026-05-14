<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle"></i> Tạo hóa đơn mới</h2>
    <a href="/admin/invoices" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/invoices/create">
            <div class="mb-3">
                <label for="booking_id" class="form-label">Chọn Booking</label>
                <select class="form-select" id="booking_id" name="booking_id" required>
                    <option value="">-- Chọn booking --</option>
                    <?php foreach ($bookings as $booking): ?>
                    <option value="<?= $booking['BookingID'] ?>">
                        <?= $booking['BookingCode'] ?> - <?= $booking['TourName'] ?> - <?= number_format($booking['TotalAmount'], 0, ',', '.') ?>đ
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Hóa đơn sẽ được tạo dựa trên tổng tiền của booking đã chọn.
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus"></i> Tạo hóa đơn
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>