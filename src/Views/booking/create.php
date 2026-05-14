<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-ticket-alt"></i> Đặt tour: <?= htmlspecialchars($tour['TourName']) ?></h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/booking/store" id="bookingForm">
                    <input type="hidden" name="schedule_id" value="<?= $schedule['ScheduleID'] ?? '' ?>">
                    <input type="hidden" name="final_voucher_code" id="final_voucher_code" value="">
                    <input type="hidden" name="discount_amount" id="discount_amount_hidden" value="0">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số lượng người lớn *</label>
                            <input type="number" name="adult_count" class="form-control" min="1" value="1" required id="adultCount">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số lượng trẻ em</label>
                            <input type="number" name="child_count" class="form-control" min="0" value="0" id="childCount">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Danh sách khách đi tour</label>
                        <div id="passengerList">
                            <div class="passenger-item row mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="passenger_name[]" class="form-control" placeholder="Họ tên">
                                </div>
                                <div class="col-md-4">
                                    <select name="passenger_type[]" class="form-control">
                                        <option value="Adult">Người lớn</option>
                                        <option value="Child">Trẻ em</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addPassenger()">
                            <i class="fas fa-plus"></i> Thêm khách
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Yêu cầu đặc biệt (nếu có)"></textarea>
                    </div>
                    
                    <!-- Ô nhập voucher với nút áp dụng AJAX -->
                    <div class="mb-3">
                        <label class="form-label">Mã giảm giá (Voucher)</label>
                        <div class="input-group">
                            <input type="text" id="voucher_code" class="form-control" placeholder="Nhập mã voucher...">
                            <button type="button" id="apply_voucher_btn" class="btn btn-outline-primary">Áp dụng</button>
                        </div>
                        <div id="voucher_message" class="mt-2 small"></div>
                    </div>
                    
                    <!-- Hiển thị giá và tổng tiền -->
                    <div class="alert alert-info" id="price_display">
                        <strong>Giá tour:</strong> <?= number_format($schedule['Price'] ?? 0, 0, ',', '.') ?>đ / người lớn<br>
                        <strong>Giá trẻ em:</strong> <?= number_format(($schedule['Price'] ?? 0) * 0.7, 0, ',', '.') ?>đ (giảm 30%)<br>
                        <hr>
                        <strong>Tạm tính:</strong> <span id="temp_total">0</span>đ<br>
                        <strong id="discount_label" style="display:none;">Giảm giá:</strong> <span id="discount_amount_display" style="display:none;">0</span>đ<br>
                        <strong>Tổng cộng:</strong> <span id="final_total">0</span>đ
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Xác nhận đặt tour</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin tour</h5>
            </div>
            <div class="card-body">
                <p><strong>Tên tour:</strong> <?= htmlspecialchars($tour['TourName']) ?></p>
                <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['TourCode']) ?></p>
                <p><strong>Thời gian:</strong> <?= $tour['Duration'] ?> ngày</p>
                <?php if ($schedule): ?>
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['StartDate'])) ?></p>
                    <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($schedule['EndDate'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let price = <?= $schedule['Price'] ?? 0 ?>;
let appliedDiscount = 0;

function addPassenger() {
    const container = document.getElementById('passengerList');
    const div = document.createElement('div');
    div.className = 'passenger-item row mb-2';
    div.innerHTML = `
        <div class="col-md-8">
            <input type="text" name="passenger_name[]" class="form-control" placeholder="Họ tên">
        </div>
        <div class="col-md-4">
            <select name="passenger_type[]" class="form-control">
                <option value="Adult">Người lớn</option>
                <option value="Child">Trẻ em</option>
            </select>
        </div>
    `;
    container.appendChild(div);
}

function calculateTotal() {
    let adult = parseInt($('#adultCount').val()) || 0;
    let child = parseInt($('#childCount').val()) || 0;
    let tempTotal = (adult * price) + (child * price * 0.7);
    let finalTotal = tempTotal - appliedDiscount;
    if (finalTotal < 0) finalTotal = 0;
    
    $('#temp_total').text(tempTotal.toLocaleString('vi-VN'));
    $('#final_total').text(finalTotal.toLocaleString('vi-VN'));
    
    if (appliedDiscount > 0) {
        $('#discount_label').show();
        $('#discount_amount_display').text(appliedDiscount.toLocaleString('vi-VN')).show();
    } else {
        $('#discount_label').hide();
        $('#discount_amount_display').hide();
    }
}

$(document).ready(function() {
    $('#adultCount, #childCount').on('change keyup', calculateTotal);
    calculateTotal();
    
    $('#apply_voucher_btn').click(function() {
        let voucherCode = $('#voucher_code').val();
        if (!voucherCode) {
            $('#voucher_message').html('<span class="text-warning">⚠️ Vui lòng nhập mã voucher</span>');
            return;
        }
        
        let adult = parseInt($('#adultCount').val()) || 0;
        let child = parseInt($('#childCount').val()) || 0;
        let tempTotal = (adult * price) + (child * price * 0.7);
        
        $.ajax({
            url: '/booking/applyVoucher',
            type: 'POST',
            data: { voucher_code: voucherCode, total_amount: tempTotal },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    appliedDiscount = parseFloat(res.discount_amount);
                    $('#final_voucher_code').val(voucherCode);
                    $('#discount_amount_hidden').val(appliedDiscount);
                    calculateTotal();
                    $('#voucher_message').html('<span class="text-success">✅ ' + res.message + '</span>');
                } else {
                    appliedDiscount = 0;
                    $('#final_voucher_code').val('');
                    $('#discount_amount_hidden').val(0);
                    calculateTotal();
                    $('#voucher_message').html('<span class="text-danger">❌ ' + res.message + '</span>');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                $('#voucher_message').html('<span class="text-danger">❌ Lỗi kết nối: ' + error + '</span>');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>