<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-edit"></i> Sửa voucher</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/voucher/update/<?= $voucher['VoucherID'] ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã voucher *</label>
                    <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($voucher['VoucherCode']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên voucher *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($voucher['VoucherName']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số tiền giảm *</label>
                    <input type="number" name="discount_value" class="form-control" value="<?= $voucher['DiscountValue'] ?>" step="1000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đơn hàng tối thiểu</label>
                    <input type="number" name="min_order_value" class="form-control" value="<?= $voucher['MinOrderValue'] ?>" step="1000">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số lượng *</label>
                    <input type="number" name="quantity" class="form-control" value="<?= $voucher['Quantity'] ?>" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày bắt đầu *</label>
                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime($voucher['StartDate'])) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày kết thúc *</label>
                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime($voucher['EndDate'])) ?>" required>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" value="1" <?= $voucher['IsActive'] ? 'checked' : '' ?>>
                <label class="form-check-label">Hoạt động</label>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="/admin/vouchers" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>