<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <h1><?= htmlspecialchars($tour['TourName']) ?></h1>
        <p class="text-muted">Mã tour: <?= htmlspecialchars($tour['TourCode']) ?> | Thời gian: <?= $tour['Duration'] ?> ngày</p>
        
        <div class="my-4">
            <h3>Mô tả tour</h3>
            <p><?= nl2br(htmlspecialchars($tour['Description'] ?? 'Chưa có mô tả')) ?></p>
        </div>
        
        <div class="my-4">
            <h3>Dịch vụ bao gồm</h3>
            <p><?= nl2br(htmlspecialchars($tour['IncludedServices'] ?? 'Chưa có thông tin')) ?></p>
        </div>
        
        <?php if (!empty($schedules)): ?>
        <div class="my-4">
            <h3>Lịch khởi hành</h3>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Ngày khởi hành</th><th>Ngày kết thúc</th><th>Giá</th><th>Còn chỗ</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($schedule['StartDate'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($schedule['EndDate'])) ?></td>
                        <td><?= number_format($schedule['Price'], 0, ',', '.') ?>đ</td>
                        <td><?= $schedule['AvailableSlots'] ?> / <?= $schedule['TotalSlots'] ?></td>
                        <td>
                            <?php if ($schedule['AvailableSlots'] > 0): ?>
                                <a href="/booking/create/<?= $tour['TourID'] ?>?schedule_id=<?= $schedule['ScheduleID'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-calendar-check"></i> Đặt ngay
                                </a>
                            <?php else: ?>
                                <span class="text-danger">Hết chỗ</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Thông tin liên hệ</h5>
                <p><i class="fas fa-phone"></i> Hotline: 1900 1234</p>
                <p><i class="fas fa-envelope"></i> Email: booking@bookingtour.com</p>
                <hr>
                <a href="/booking/create/<?= $tour['TourID'] ?>" class="btn btn-success w-100">
                    <i class="fas fa-ticket-alt"></i> Đặt tour ngay
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>