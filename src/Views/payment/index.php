<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Thanh toán đơn hàng</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Thông tin đơn hàng</h5>
                    <table class="table">
                        <tr>
                            <th>Mã booking</th>
                            <td><?= $booking['BookingCode'] ?></td>
                        </tr>
                        <tr>
                            <th>Tour</th>
                            <td><?= $booking['TourName'] ?></td>
                        </tr>
                        <tr>
                            <th>Tổng tiền</th>
                            <td class="text-danger fw-bold"><?= number_format($amount, 0, ',', '.') ?>đ</td>
                        </tr>
                    </table>
                    
                    <h5 class="mt-4">Chọn phương thức thanh toán</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="/payment/create-momo/<?= $booking['BookingID'] ?>" class="btn btn-primary w-100">
                                Thanh toán qua Momo
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/payment/create-vnpay/<?= $booking['BookingID'] ?>" class="btn btn-primary w-100">
                                Thanh toán qua VNPay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>