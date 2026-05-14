<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-key"></i> Quên mật khẩu</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <p class="text-muted">Vui lòng nhập tên đăng nhập và email của bạn. Mật khẩu mới sẽ được gửi về email.</p>
                
                <form method="POST" action="/forgot/request">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Gửi yêu cầu</button>
                </form>
                <hr>
                <p class="text-center mb-0">
                    <a href="/auth/login">← Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>