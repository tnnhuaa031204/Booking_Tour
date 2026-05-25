<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-umbrella-beach"></i> Quản lý tour</h2>
    
    <!-- Nút Thêm tour mới - Chỉ Admin và Manager -->
    <?php if (hasPermission('TOUR_CREATE')): ?>
    <a href="/admin/tours/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm tour mới
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
                        <th>Mã tour</th>
                        <th>Tên tour</th>
                        <th>Số ngày</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tours)): ?>
                        <tr><td colspan="7" class="text-center">Chưa có tour nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td><?= $tour['TourID'] ?></td>
                            <td><?= htmlspecialchars($tour['TourCode']) ?></td>
                            <td><?= htmlspecialchars($tour['TourName']) ?></td>
                            <td><?= $tour['Duration'] ?> ngày</td>
                            <td>
                                <?php if ($tour['IsActive']): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Ngừng</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($tour['CreatedAt'])) ?></td>
                            <td>
                                <!-- Nút Sửa - Chỉ Admin và Manager -->
                                <?php if (hasPermission('TOUR_EDIT')): ?>
                                <a href="/admin/tours/edit/<?= $tour['TourID'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <?php endif; ?>
                                
                                <!-- Nút Xóa - Chỉ Admin và Manager -->
                                <?php if (hasPermission('TOUR_DELETE')): ?>
                                <a href="/admin/tours/delete/<?= $tour['TourID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa tour này?')">
                                    <i class="fas fa-trash"></i> Xóa
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