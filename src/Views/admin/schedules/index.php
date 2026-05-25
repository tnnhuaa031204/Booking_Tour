<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-alt"></i> Lịch khởi hành</h2>
    
    <!-- Nút Thêm lịch mới - Chỉ Admin và Manager -->
    <?php if (hasPermission('SCHEDULE_CREATE')): ?>
    <a href="/admin/schedule/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm lịch mới
    </a>
    <?php endif; ?>
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
                                // Dùng parameterized map để tránh lỗi encoding khi so sánh
                                $st = $item['Status'] ?? '';
                                $statusMap = [
                                    'ng m' => ['success', 'Đang mở'],
                                    'kín'  => ['warning', 'Đã kín'],
                                    'hành' => ['info',    'Đã khởi hành'],
                                    'ủy'   => ['danger',  'Đã hủy'],
                                ];
                                $statusClass = 'secondary';
                                $statusLabel = $st ?: 'Đang mở';
                                foreach ($statusMap as $key => [$cls, $label]) {
                                    if (strpos($st, $key) !== false) {
                                        $statusClass = $cls;
                                        $statusLabel = $label;
                                        break;
                                    }
                                }
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            </td>
                            <td>
                                <!-- Nút Sửa - Chỉ Admin và Manager -->
                                <?php if (hasPermission('SCHEDULE_EDIT')): ?>
                                <a href="/admin/schedule/edit/<?= $item['ScheduleID'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <?php endif; ?>
                                
                                <!-- Nút Xóa - Chỉ Admin và Manager -->
                                <?php if (hasPermission('SCHEDULE_DELETE')): ?>
                                <a href="/admin/schedule/delete/<?= $item['ScheduleID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch khởi hành này?')">Xóa</a>
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