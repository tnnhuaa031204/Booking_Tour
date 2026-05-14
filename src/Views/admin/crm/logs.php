<?php
require_once __DIR__ . '/../../../Views/layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history"></i> Lịch sử tương tác CRM</h2>
    <a href="/admin/crm/logs/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm ghi chú
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th>Loại</th>
                        <th>Nội dung</th>
                        <th>Ngày</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Chưa có ghi chú nào</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['LogID'] ?></td>
                        <td><?= $log['CustomerName'] ?></td>
                        <td><?= $log['EmployeeName'] ?></td>
                        <td>
                            <span class="badge bg-info"><?= $log['InteractionType'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($log['Content'] ?? '') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($log['CreatedAt'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../Views/layouts/admin_footer.php'; ?>