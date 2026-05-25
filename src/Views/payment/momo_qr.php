<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-qrcode"></i> Quét mã QR để thanh toán</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <img src="<?= $qrData['payUrl'] ?>" alt="Momo QR" style="width: 250px; height: 250px;">
                    <p class="mt-3">Quét mã QR bằng ứng dụng Momo</p>
                </div>
                <div class="col-md-6">
                    <h5>Thông tin đơn hàng</h5>
                    <table class="table">
                        <tr><th>Mã booking</th><td><?= $booking['BookingCode'] ?></td></tr>
                        <tr><th>Tour</th><td><?= $booking['TourName'] ?></td></tr>
                        <tr><th>Số tiền</th><td class="text-danger fw-bold"><?= number_format($amount, 0, ',', '.') ?>đ</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>