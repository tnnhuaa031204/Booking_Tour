<?php require_once __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Thêm tour mới</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/tour/store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã tour *</label>
                    <input type="text" name="tour_code" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên tour *</label>
                    <input type="text" name="tour_name" class="form-control" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số ngày *</label>
                    <input type="number" name="duration" class="form-control" min="1" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Mô tả chi tiết về tour..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Dịch vụ bao gồm</label>
                <textarea name="included_services" class="form-control" rows="3" placeholder="Liệt kê các dịch vụ bao gồm trong tour..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu tour</button>
            <a href="/admin/tours" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>