<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-check-circle"></i> Đặt tour thành công!</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <strong>Mã booking:</strong> <?= $booking['BookingCode'] ?><br>
                    Cảm ơn bạn đã đặt tour. Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.
                </div>
                
                <h5>Thông tin đặt tour</h5>
                <table class="table table-bordered">
                    <tr><th style="width: 40%">Tour</th><td><?= htmlspecialchars($booking['TourName']) ?></td></tr>
                    <tr><th>Ngày khởi hành</th><td><?= date('d/m/Y', strtotime($booking['StartDate'])) ?></td></tr>
                    <tr><th>Số lượng người lớn</th><td><?= $booking['AdultCount'] ?></td></tr>
                    <tr><th>Số lượng trẻ em</th><td><?= $booking['ChildCount'] ?></td></tr>
                    <tr><th>Tổng tiền</th><td><strong class="text-danger"><?= number_format($booking['TotalAmount'], 0, ',', '.') ?>đ</strong></td></tr>
                    <tr><th>Trạng thái thanh toán</th><td><?= $booking['PaymentStatus'] ?></td></tr>
                </table>
                
                <h5>Danh sách khách đi tour</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>STT</th><th>Họ tên</th><th>Loại</th></thead>
                    <tbody>
                        <?php foreach ($passengers as $index => $p): ?>
                        <tr><td><?= $index + 1 ?></td><td><?= htmlspecialchars($p['FullName']) ?></td><td><?= $p['PassengerType'] == 'Adult' ? 'Người lớn' : 'Trẻ em' ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="text-center mt-4">
                    <!-- ====== NÚT THANH TOÁN MỚI ====== -->
                    <?php if ($booking['PaymentStatus'] == 'Chưa thanh toán'): ?>
                    <a href="/payment/index/<?= $booking['BookingID'] ?>" class="btn btn-warning btn-lg">
                        <i class="fas fa-credit-card"></i> Thanh toán ngay
                    </a>
                    <?php endif; ?>
                    <!-- ================================== -->
                    
                    <a href="/" class="btn btn-primary">Về trang chủ</a>
                    <a href="/booking/history" class="btn btn-outline-primary">Xem lịch sử đặt tour</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>