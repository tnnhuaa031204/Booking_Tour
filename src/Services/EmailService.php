<?php
// src/Services/EmailService.php

class EmailService {
    
    /**
     * Hàm gửi email cơ bản
     */
    public function send($to, $subject, $body) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: BookingTour <noreply@bookingtour.com>" . "\r\n";
        
        return mail($to, $subject, $body, $headers);
    }

    /**
     * Gửi email xác nhận đặt tour thành công
     */
    public function sendBookingConfirmation($toEmail, $customerName, $bookingData) {
        $subject = 'Xác nhận đặt tour thành công - BookingTour';
        
        // Nội dung email HTML
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
                .header { background: #007bff; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .total { font-weight: bold; color: #28a745; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Cảm ơn quý khách đã đặt tour!</h2>
                </div>
                <div class='content'>
                    <p>Kính gửi <strong>{$customerName}</strong>,</p>
                    <p>Đơn đặt tour của quý khách đã được ghi nhận thành công. Chi tiết đơn hàng:</p>
                    
                    <table class='table'>
                        <tr>
                            <th>Mã booking</th>
                            <td><strong>{$bookingData['booking_code']}</strong></td>
                        </tr>
                        <tr>
                            <th>Tên tour</th>
                            <td>{$bookingData['tour_name']}</td>
                        </tr>
                        <tr>
                            <th>Ngày khởi hành</th>
                            <td>{$bookingData['start_date']}</td>
                        </tr>
                        <tr>
                            <th>Số lượng</th>
                            <td>{$bookingData['adult_count']} người lớn + {$bookingData['child_count']} trẻ em</td>
                        </tr>
                        <tr>
                            <th>Tổng tiền</th>
                            <td>" . number_format($bookingData['total_amount'], 0, ',', '.') . " VNĐ</td>
                        </tr>
                        <tr>
                            <th>Giảm giá</th>
                            <td>" . number_format($bookingData['discount_amount'], 0, ',', '.') . " VNĐ</td>
                        </tr>
                        <tr>
                            <th>Thành tiền</th>
                            <td class='total'>" . number_format($bookingData['final_amount'], 0, ',', '.') . " VNĐ</td>
                        </tr>
                    </table>
                    
                    <p><strong>Trạng thái thanh toán:</strong> {$bookingData['payment_status']}</p>
                    <p>Vui lòng thanh toán để hoàn tất đặt tour.</p>
                    
                    <p>Trân trọng,<br>
                    <strong>BookingTour Team</strong></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Gửi email
        return $this->send($toEmail, $subject, $body);
    }
}
?>