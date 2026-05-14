<?php
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice-dollar"></i> Chi tiết hóa đơn</h2>
    <a href="/admin/invoices" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($invoice): ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>Số hóa đơn</th>
                        <td><strong><?= $invoice['InvoiceNumber'] ?></strong></td>
                    </tr>
                    <tr>
                        <th>Booking</th>
                        <td><?= $invoice['BookingCode'] ?></td>
                    </tr>
                    <tr>
                        <th>Khách hàng</th>
                        <td><?= $invoice['CustomerName'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $invoice['CustomerEmail'] ?></td>
                    </tr>
                    <tr>
                        <th>Ngày lập</th>
                        <td><?= date('d/m/Y', strtotime($invoice['InvoiceDate'])) ?></td>
                    </tr>
                    <tr>
                        <th>Tổng tiền</th>
                        <td class="text-danger fw-bold"><?= number_format($invoice['TotalAmount'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($invoice['IsSentEmail']): ?>
                                <span class="badge bg-success">Đã gửi email</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Chưa gửi email</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <strong>Thông tin tour</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Tour:</strong> <?= $invoice['TourName'] ?? 'N/A' ?></p>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($invoice['StartDate'] ?? '')) ?></p>
                        <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($invoice['EndDate'] ?? '')) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <?php if (!$invoice['IsSentEmail']): ?>
            <a href="/admin/invoices/send-email/<?= $invoice['InvoiceID'] ?>" class="btn btn-warning">
                <i class="fas fa-envelope"></i> Gửi email
            </a>
            <?php endif; ?>
            <a href="/admin/invoices/pdf/<?= $invoice['InvoiceID'] ?>" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Xuất PDF
            </a>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">Không tìm thấy hóa đơn</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>