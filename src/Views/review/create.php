<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4" style="max-width:680px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-star"></i> Đánh giá tour</h5>
        </div>
        <div class="card-body">

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Thông tin tour -->
            <div class="alert alert-light border mb-4">
                <strong><i class="fas fa-umbrella-beach text-primary"></i> <?= htmlspecialchars($booking['TourName']) ?></strong><br>
                <small class="text-muted">
                    Mã booking: <?= htmlspecialchars($booking['BookingCode']) ?> &nbsp;|&nbsp;
                    <?= date('d/m/Y', strtotime($booking['StartDate'])) ?> – <?= date('d/m/Y', strtotime($booking['EndDate'])) ?>
                </small>
            </div>

            <form method="POST" action="/review/store">
                <input type="hidden" name="booking_id" value="<?= $booking['BookingID'] ?>">

                <!-- Chọn số sao -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Xếp hạng của bạn *</label>
                    <div class="star-rating d-flex gap-2 fs-2">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" class="d-none" <?= $i === 5 ? 'checked' : '' ?>>
                        <label for="star<?= $i ?>" class="star-label" style="cursor:pointer; color:#ccc;">&#9733;</label>
                        <?php endfor; ?>
                    </div>
                    <small class="text-muted" id="ratingText">Xuất sắc</small>
                </div>

                <!-- Nhận xét -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Nhận xét của bạn</label>
                    <textarea name="comment" class="form-control" rows="5"
                              placeholder="Chia sẻ trải nghiệm của bạn về tour này..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi đánh giá
                    </button>
                    <a href="/booking/history" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-rating { flex-direction: row-reverse; justify-content: flex-end; }
.star-label { transition: color 0.15s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #ffc107 !important; }
</style>

<script>
const ratingLabels = { 1: 'Tệ', 2: 'Không hài lòng', 3: 'Bình thường', 4: 'Tốt', 5: 'Xuất sắc' };
document.querySelectorAll('input[name="rating"]').forEach(input => {
    input.addEventListener('change', () => {
        document.getElementById('ratingText').textContent = ratingLabels[input.value];
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>