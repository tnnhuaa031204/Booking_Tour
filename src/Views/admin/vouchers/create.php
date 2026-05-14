<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Thêm voucher mới</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/voucher/store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã voucher *</label>
                    <input type="text" name="code" class="form-control" placeholder="SUMMER2025" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên voucher *</label>
                    <input type="text" name="name" class="form-control" placeholder="Khuyến mãi mùa hè" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số tiền giảm *</label>
                    <input type="number" name="discount_value" class="form-control" step="1000" placeholder="50000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đơn hàng tối thiểu</label>
                    <input type="number" name="min_order_value" class="form-control" step="1000" value="0">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số lượng *</label>
                    <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày bắt đầu *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày kết thúc *</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="/admin/vouchers" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>