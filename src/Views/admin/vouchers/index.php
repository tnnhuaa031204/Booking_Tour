<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tag"></i> Quản lý Voucher</h2>
    <a href="/admin/voucher/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm voucher mới
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
                        <th>Mã voucher</th>
                        <th>Tên voucher</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Đơn tối thiểu</th>
                        <th>Số lượng</th>
                        <th>Đã dùng</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr><td colspan="12" class="text-center">Chưa có voucher nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $item): ?>
                        <tr>
                            <td><?= $item['VoucherID'] ?></td>
                            <td><?= htmlspecialchars($item['VoucherCode']) ?></td>
                            <td><?= htmlspecialchars($item['VoucherName']) ?></td>
                            <td><?= $item['DiscountType'] == 'Percent' ? '%' : 'Số tiền' ?></td>
                            <td>
                                <?= $item['DiscountType'] == 'Percent' ? $item['DiscountValue'] . '%' : number_format($item['DiscountValue'], 0, ',', '.') . 'đ' ?>
                            </td>
                            <td><?= number_format($item['MinOrderValue'], 0, ',', '.') ?>đ</td>
                            <td><?= $item['Quantity'] ?></td>
                            <td><?= $item['UsedCount'] ?></td>
                            <td><?= date('d/m/Y', strtotime($item['StartDate'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($item['EndDate'])) ?></td>
                            <td>
                                <?php if ($item['IsActive']): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tạm dừng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/voucher/edit/<?= $item['VoucherID'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <a href="/admin/voucher/delete/<?= $item['VoucherID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa voucher này?')">Xóa</a>
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