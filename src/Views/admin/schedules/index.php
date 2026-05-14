<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-alt"></i> Lịch khởi hành</h2>
    <a href="/admin/schedule/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm lịch mới
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tour</th>
                        <th>Ngày khởi hành</th>
                        <th>Ngày kết thúc</th>
                        <th>Giá</th>
                        <th>Số chỗ</th>
                        <th>Còn trống</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr><td colspan="9" class="text-center">Chưa có lịch khởi hành nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $item): ?>
                        <tr>
                            <td><?= $item['ScheduleID'] ?></td>
                            <td><?= htmlspecialchars($item['TourName']) ?> (<small><?= $item['TourCode'] ?></small>)</td>
                            <td><?= date('d/m/Y', strtotime($item['StartDate'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($item['EndDate'])) ?></td>
                            <td><?= number_format($item['Price'], 0, ',', '.') ?>đ</td>
                            <td><?= $item['TotalSlots'] ?></td>
                            <td><?= $item['AvailableSlots'] ?></td>
                            <td>
                                <?php
                                $statusClass = match($item['Status']) {
                                    'Đang mở' => 'success',
                                    'Đã kín' => 'warning',
                                    'Đã khởi hành' => 'info',
                                    'Hủy' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $item['Status'] ?></span>
                            </td>
                            <td>
                                <a href="/admin/schedule/edit/<?= $item['ScheduleID'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <a href="/admin/schedule/delete/<?= $item['ScheduleID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch khởi hành này?')">Xóa</a>
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