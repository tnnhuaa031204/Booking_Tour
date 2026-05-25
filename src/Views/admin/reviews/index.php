<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-star text-warning"></i> Quản lý Đánh giá</h2>
    <div>
        <span class="badge bg-danger fs-6">
            <?= count(array_filter($reviews, fn($r) => !$r['IsVisible'])) ?> chưa duyệt
        </span>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Tab chưa duyệt / tất cả -->
<ul class="nav nav-tabs mb-3" id="reviewTabs">
    <li class="nav-item">
        <a class="nav-link active" id="tab-pending" href="#" onclick="filterTab('pending', this)">
            <i class="fas fa-clock"></i> Chờ duyệt
            <span class="badge bg-danger ms-1" id="pendingCount">
                <?= count(array_filter($reviews, fn($r) => !$r['IsVisible'])) ?>
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-all" href="#" onclick="filterTab('all', this)">
            <i class="fas fa-list"></i> Tất cả
            <span class="badge bg-secondary ms-1"><?= count($reviews) ?></span>
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Khách hàng</th>
                        <th>Tour</th>
                        <th style="width:110px">Đánh giá</th>
                        <th>Nội dung</th>
                        <th style="width:110px">Trạng thái</th>
                        <th style="width:90px">Ngày</th>
                        <th style="width:170px">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="reviewTableBody">
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có đánh giá nào</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <tr class="review-row <?= $review['IsVisible'] ? 'row-approved' : 'row-pending' ?>"
                        data-status="<?= $review['IsVisible'] ? 'approved' : 'pending' ?>">
                        <td><?= $review['ReviewID'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($review['CustomerName']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($review['TourName']) ?></td>
                        <td>
                            <div class="text-warning">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $review['Rating'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted"><?= $review['Rating'] ?>/5</small>
                        </td>
                        <td>
                            <?php $comment = $review['Comment'] ?? ''; ?>
                            <?php if (strlen($comment) > 60): ?>
                                <span><?= htmlspecialchars(mb_substr($comment, 0, 60)) ?>...</span>
                                <a href="#" class="text-primary small"
                                   onclick="showDetail(<?= $review['ReviewID'] ?>, <?= htmlspecialchars(json_encode([
                                       'name'    => $review['CustomerName'],
                                       'tour'    => $review['TourName'],
                                       'rating'  => $review['Rating'],
                                       'comment' => $review['Comment'],
                                       'date'    => date('d/m/Y H:i', strtotime($review['CreatedAt'])),
                                   ])) ?>); return false;">
                                    Xem thêm
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars($comment ?: '(Không có nhận xét)') ?>
                                <?php if ($comment): ?>
                                <a href="#" class="text-primary small ms-1"
                                   onclick="showDetail(<?= $review['ReviewID'] ?>, <?= htmlspecialchars(json_encode([
                                       'name'    => $review['CustomerName'],
                                       'tour'    => $review['TourName'],
                                       'rating'  => $review['Rating'],
                                       'comment' => $review['Comment'],
                                       'date'    => date('d/m/Y H:i', strtotime($review['CreatedAt'])),
                                   ])) ?>); return false;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($review['IsVisible']): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i> Đã duyệt</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= date('d/m/Y', strtotime($review['CreatedAt'])) ?></td>
                        <td>
                            <?php if (!$review['IsVisible']): ?>
                                <!-- Chờ duyệt: nút Duyệt + Từ chối -->
                                <a href="/admin/reviews/approve/<?= $review['ReviewID'] ?>"
                                   class="btn btn-sm btn-success mb-1"
                                   title="Duyệt - hiển thị công khai">
                                    <i class="fas fa-check"></i> Duyệt
                                </a>
                                <a href="#"
                                   class="btn btn-sm btn-danger mb-1"
                                   title="Từ chối - xóa đánh giá"
                                   onclick="confirmReject(<?= $review['ReviewID'] ?>); return false;">
                                    <i class="fas fa-times"></i> Từ chối
                                </a>
                            <?php else: ?>
                                <!-- Đã duyệt: nút Ẩn -->
                                <a href="/admin/reviews/hide/<?= $review['ReviewID'] ?>"
                                   class="btn btn-sm btn-warning mb-1"
                                   title="Ẩn khỏi hiển thị công khai">
                                    <i class="fas fa-eye-slash"></i> Ẩn
                                </a>
                                <a href="#"
                                   class="btn btn-sm btn-danger mb-1"
                                   onclick="confirmReject(<?= $review['ReviewID'] ?>); return false;">
                                    <i class="fas fa-trash"></i> Xóa
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

<!-- Modal xem chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-star text-warning"></i> Chi tiết đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr><th style="width:120px">Khách hàng</th><td id="d-name"></td></tr>
                    <tr><th>Tour</th><td id="d-tour"></td></tr>
                    <tr>
                        <th>Xếp hạng</th>
                        <td>
                            <span id="d-stars" class="text-warning fs-5"></span>
                            <span id="d-rating" class="text-muted ms-1"></span>
                        </td>
                    </tr>
                    <tr><th>Ngày gửi</th><td id="d-date"></td></tr>
                </table>
                <div class="mt-2">
                    <strong>Nội dung nhận xét:</strong>
                    <div id="d-comment" class="mt-1 p-3 bg-light rounded" style="white-space:pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer" id="d-actions"></div>
        </div>
    </div>
</div>

<!-- Form ẩn để POST từ chối/xóa -->
<form id="rejectForm" method="GET" action="">
</form>

<script>
// Tab filter
function filterTab(type, el) {
    document.querySelectorAll('#reviewTabs .nav-link').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.review-row').forEach(row => {
        if (type === 'all') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.status === type ? '' : 'none';
        }
    });
}

// Mặc định hiện tab pending
document.addEventListener('DOMContentLoaded', () => {
    filterTab('pending', document.getElementById('tab-pending'));
});

// Modal chi tiết
function showDetail(id, data) {
    document.getElementById('d-name').textContent    = data.name;
    document.getElementById('d-tour').textContent    = data.tour;
    document.getElementById('d-date').textContent    = data.date;
    document.getElementById('d-rating').textContent  = data.rating + '/5';
    document.getElementById('d-comment').textContent = data.comment || '(Không có nhận xét)';

    let stars = '';
    for (let i = 1; i <= 5; i++) stars += i <= data.rating ? '★' : '☆';
    document.getElementById('d-stars').textContent = stars;

    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
}

// Xác nhận từ chối/xóa
function confirmReject(id) {
    if (confirm('Bạn có chắc muốn TỪ CHỐI và xóa đánh giá này?\nHành động này không thể hoàn tác.')) {
        window.location.href = '/admin/reviews/delete/' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>