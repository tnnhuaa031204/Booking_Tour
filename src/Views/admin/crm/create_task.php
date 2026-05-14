<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle"></i> Tạo task mới</h2>
    <a href="/admin/crm/tasks" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/crm/tasks/create">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Khách hàng (tùy chọn)</label>
                <select class="form-select" id="customer_id" name="customer_id">
                    <option value="">-- Không chọn --</option>
                    <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['CustomerID'] ?>"><?= $customer['FullName'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn hoàn thành</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Tạo task
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>