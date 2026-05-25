<?php
// src/Services/MomoService.php

class MomoService {
    
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    
    public function __construct() {
        $db = db();
        $stmt = $db->query("SELECT ConfigKey, ConfigValue FROM PaymentConfigs");
        $configs = [];
        while ($row = $stmt->fetch()) {
            $configs[$row['ConfigKey']] = $row['ConfigValue'];
        }
        
        $this->partnerCode = $configs['MOMO_PARTNER_CODE'] ?? '';
        $this->accessKey   = $configs['MOMO_ACCESS_KEY']   ?? '';
        $this->secretKey   = $configs['MOMO_SECRET_KEY']   ?? '';
        $this->endpoint    = $configs['MOMO_ENDPOINT']     ?? 'https://test-payment.momo.vn/v2/gateway/api/create';
    }
    
    /**
     * Tạo link thanh toán MoMo (chuyển hướng đến trang MoMo)
     * Dùng cho createMomo() trong PaymentController
     */
    public function createPayment($bookingId, $amount, $returnUrl, $notifyUrl) {
        $orderId   = 'MOMO_' . date('YmdHis') . '_' . $bookingId;
        $requestId = 'REQ_'  . date('YmdHis') . '_' . rand(1000, 9999);
        $orderInfo = 'Thanh toan booking tour: ' . $bookingId;
        $extraData = base64_encode('booking_id=' . $bookingId);
        $requestType = 'captureWallet';

        $rawHash = "accessKey="   . $this->accessKey
                 . "&amount="     . $amount
                 . "&extraData="  . $extraData
                 . "&ipnUrl="     . $notifyUrl
                 . "&orderId="    . $orderId
                 . "&orderInfo="  . $orderInfo
                 . "&partnerCode=". $this->partnerCode
                 . "&redirectUrl=". $returnUrl
                 . "&requestId="  . $requestId
                 . "&requestType=". $requestType;

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'accessKey'   => $this->accessKey,
            'requestId'   => $requestId,
            'amount'      => (string)$amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        return $this->callApi($data);
    }
    
    /**
     * Tạo QR thanh toán MoMo
     * Dùng cho qrMomo() trong PaymentController
     */
    public function createQRPayment($bookingId, $amount) {
        $returnUrl = 'http://localhost:8000/payment/callback';
        $notifyUrl = 'http://localhost:8000/payment/notify';
        return $this->createPayment($bookingId, $amount, $returnUrl, $notifyUrl);
    }

    /**
     * Xác minh chữ ký từ callback MoMo
     */
    public function verifySignature($data) {
        $rawHash = "accessKey="   . $this->accessKey
                 . "&amount="     . $data['amount']
                 . "&extraData="  . $data['extraData']
                 . "&message="    . $data['message']
                 . "&orderId="    . $data['orderId']
                 . "&orderInfo="  . $data['orderInfo']
                 . "&orderType="  . $data['orderType']
                 . "&partnerCode=". $data['partnerCode']
                 . "&payType="    . $data['payType']
                 . "&requestId="  . $data['requestId']
                 . "&responseTime=". $data['responseTime']
                 . "&resultCode=" . $data['resultCode']
                 . "&transId="    . $data['transId'];

        $expected = hash_hmac('sha256', $rawHash, $this->secretKey);
        return $expected === ($data['signature'] ?? '');
    }

    /**
     * Gọi API MoMo
     */
    private function callApi($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) return null;
        return json_decode($response, true);
    }
}
?>