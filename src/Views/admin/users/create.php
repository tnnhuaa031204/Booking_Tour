<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-plus"></i> Thêm người dùng mới (Nhân viên)</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/users/store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên đăng nhập *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Vai trò *</label>
                    <select name="role_id" class="form-control" required>
                        <option value="">-- Chọn vai trò --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['RoleID'] ?>"><?= $role['RoleName'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Thêm người dùng</button>
            <a href="/admin/users" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>