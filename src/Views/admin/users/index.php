<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Quản lý người dùng</h2>
    <a href="/admin/users/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm người dùng
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
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="9" class="text-center">Chưa có người dùng</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['UserID'] ?></td>
                            <td><?= htmlspecialchars($user['Username']) ?></td>
                            <td><?= htmlspecialchars($user['FullName']) ?></td>
                            <td><?= htmlspecialchars($user['Email']) ?></td>
                            <td><?= htmlspecialchars($user['Phone']) ?></td>
                            <td><?= $user['RoleName'] ?></td>
                            <td>
                                <?php if ($user['IsActive']): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Khóa</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($user['CreatedAt'])) ?></td>
                            <td>
                                <a href="/admin/users/toggle/<?= $user['UserID'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Đổi trạng thái tài khoản này?')">
                                    <?= $user['IsActive'] ? 'Khóa' : 'Mở khóa' ?>
                                </a>
                                <?php if ($user['RoleName'] == 'Customer'): ?>
                                    <a href="/admin/users/delete/<?= $user['UserID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa người dùng này? (Chỉ Customer)')">Xóa</a>
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