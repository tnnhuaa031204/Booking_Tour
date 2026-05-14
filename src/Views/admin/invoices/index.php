<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice-dollar"></i> Quản lý Hóa đơn</h2>
    <a href="/admin/invoices/create" class="btn btn-success">
        <i class="fas fa-plus"></i> Tạo hóa đơn mới
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Số hóa đơn</th>
                        <th>Booking</th>
                        <th>Khách hàng</th>
                        <th>Ngày lập</th>
                        <th>Tổng tiền</th>
                        <th>Đã gửi email</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Chưa có hóa đơn nào</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?= $invoice['InvoiceID'] ?></td>
                        <td><strong><?= $invoice['InvoiceNumber'] ?></strong></td>
                        <td><?= $invoice['BookingCode'] ?></td>
                        <td><?= $invoice['CustomerName'] ?></td>
                        <td><?= date('d/m/Y', strtotime($invoice['InvoiceDate'])) ?></td>
                        <td><?= number_format($invoice['TotalAmount'], 0, ',', '.') ?>đ</td>
                        <td>
                            <?php if ($invoice['IsSentEmail']): ?>
                                <span class="badge bg-success">Đã gửi</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Chưa gửi</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/invoices/detail/<?= $invoice['InvoiceID'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            <?php if (!$invoice['IsSentEmail']): ?>
                            <a href="/admin/invoices/send-email/<?= $invoice['InvoiceID'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-envelope"></i> Gửi email
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