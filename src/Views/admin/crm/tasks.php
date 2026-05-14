<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tasks"></i> Quản lý Tasks</h2>
    <a href="/admin/crm/tasks/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Tạo task mới
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th>Hạn</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Chưa có task nào</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= $task['TaskID'] ?></td>
                        <td><?= $task['Title'] ?></td>
                        <td><?= $task['CustomerName'] ?? 'Không có' ?></td>
                        <td><?= $task['EmployeeName'] ?></td>
                        <td><?= date('d/m/Y', strtotime($task['DueDate'])) ?></td>
                        <td>
                            <?php if ($task['IsCompleted']): ?>
                                <span class="badge bg-success">Hoàn thành</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Chưa hoàn thành</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$task['IsCompleted']): ?>
                            <a href="/admin/crm/tasks/complete/<?= $task['TaskID'] ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Hoàn thành
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>