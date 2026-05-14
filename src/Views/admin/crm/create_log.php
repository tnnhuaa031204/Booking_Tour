<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle"></i> Thêm ghi chú CRM</h2>
    <a href="/admin/crm/logs" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/crm/logs/create">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Khách hàng</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['CustomerID'] ?>"><?= $customer['FullName'] ?> (<?= $customer['Phone'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="interaction_type" class="form-label">Loại tương tác</label>
                <select class="form-select" id="interaction_type" name="interaction_type" required>
                    <option value="Gọi điện">Gọi điện</option>
                    <option value="Email">Email</option>
                    <option value="Báo giá">Báo giá</option>
                    <option value="Gặp trực tiếp">Gặp trực tiếp</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Nội dung</label>
                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu ghi chú
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>