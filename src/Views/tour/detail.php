<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8">

        <!-- ====== GALLERY ẢNH ====== -->
        <?php if (!empty($images)): ?>
        <div class="mb-4">
            <!-- Ảnh lớn chính -->
            <?php $thumbnail = null; foreach ($images as $img) { if ($img['IsThumbnail']) { $thumbnail = $img; break; } } if (!$thumbnail) $thumbnail = $images[0]; ?>
            <div class="mb-2">
                <img id="mainImage"
                     src="<?= htmlspecialchars($thumbnail['ImageURL']) ?>"
                     alt="<?= htmlspecialchars($tour['TourName']) ?>"
                     class="w-100 rounded shadow"
                     style="height:400px; object-fit:cover; cursor:pointer;"
                     onclick="openLightbox(this.src)">
            </div>

            <!-- Thumbnails nhỏ -->
            <?php if (count($images) > 1): ?>
            <div class="d-flex gap-2 flex-wrap">
                <?php foreach ($images as $img): ?>
                <img src="<?= htmlspecialchars($img['ImageURL']) ?>"
                     alt="<?= htmlspecialchars($img['Caption'] ?? $tour['TourName']) ?>"
                     class="rounded border <?= $img['IsThumbnail'] ? 'border-primary border-2' : 'border-secondary' ?>"
                     style="width:80px; height:60px; object-fit:cover; cursor:pointer; transition:opacity .2s;"
                     onmouseover="this.style.opacity='.75'"
                     onmouseout="this.style.opacity='1'"
                     onclick="document.getElementById('mainImage').src=this.src; openLightbox(null, false)">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <!-- =========================== -->

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
        <div class="card shadow sticky-top" style="top:20px;">
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

<!-- Lightbox -->
<div id="lightbox" onclick="closeLightbox()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.85); z-index:9999; align-items:center; justify-content:center; cursor:zoom-out;">
    <img id="lightboxImg" src="" style="max-width:90vw; max-height:90vh; border-radius:8px; box-shadow:0 0 30px #000;">
</div>

<script>
function openLightbox(src, open = true) {
    if (!open) return;
    document.getElementById('lightboxImg').src = src;
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>