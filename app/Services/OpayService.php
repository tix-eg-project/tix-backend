<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpayService
{
    private string $merchantId;
    private string $publicKey;
    private string $secretKey;
    private string $createUrl;
    private string $statusUrl;

    // NOTE: مش هنحوّل USD خلاص — هندفع EGP مباشرة
    protected int $defaultExpireAtMin = 30; // دقائق

    public function __construct()
    {
        $this->merchantId = trim((string) config('opay.merchant_id'));
        $this->publicKey  = trim((string) config('opay.public_key'));
        $this->secretKey  = trim((string) config('opay.secret_key'));

        $this->createUrl  = config('opay.endpoints.cashier_create');
        $this->statusUrl  = config('opay.endpoints.cashier_status');

        Log::info('OPay boot', [
            'env'       => env('OPAY_ENV'),
            'createUrl' => $this->createUrl,
            'statusUrl' => $this->statusUrl,
            'merchant'  => $this->merchantId ? 'SET' : 'MISSING',
        ]);
    }

    public function initiateCashierPayment(array $data): array
    {
        try {
            // مبلغ بالجنيه مباشرة
            $egpMajor = (int) ceil((float) ($data['amount_egp'] ?? $data['amount'] ?? 0));
            if ($egpMajor <= 0) {
                throw new \Exception('Invalid amount');
            }
        } catch (\Exception $e) {
            Log::error('OpayService: amount resolve failed', ['error' => $e->getMessage()]);
            return ['status' => false, 'message' => 'Amount resolve failed: ' . $e->getMessage(), 'data' => []];
        }

        $egpMinor  = $egpMajor * 100;
        $reference = (string) $data['reference'];

        $returnUrl = 'https://updates.medicaoverseas.com/api/payment/callback?gateway=opay&reference=' . urlencode($reference);
        $cancelUrl = 'https://updates.medicaoverseas.com/api/payment/callback?gateway=opay&reference=' . urlencode($reference);

        $payload = [
            'country'     => 'EG',
            'reference'   => $reference,
            'amount'      => [
                'total'    => $egpMinor,
                'currency' => 'EGP',
            ],
            'returnUrl'   => $returnUrl,
            'cancelUrl'   => $cancelUrl,
            'callbackUrl' => 'https://updates.medicaoverseas.com/api/opay/webhook',
            'expireAt'    => (int) ($data['expireAt'] ?? $this->defaultExpireAtMin), // بالدقائق
            'userInfo'    => [
                'userEmail'  => $data['userEmail'] ?? null,
                'userId'     => $data['userId'] ?? null,
                'userMobile' => $data['userPhone'] ?? null,
                'userName'   => $data['userName'] ?? null,
            ],
            'product' => [
                'name'        => $data['product_name'] ?? 'Medica Coupons',
                'description' => $data['product_desc'] ?? 'Coupon Purchase',
            ],
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->publicKey, // PublicKey
            'MerchantId'    => $this->merchantId,            // MerchantId
            'Content-Type'  => 'application/json',
        ];

        $resp = Http::withHeaders($headers)
            ->withOptions([
                'timeout'         => 20,
                'connect_timeout' => 10,
                'curl'            => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
            ])
            ->post($this->createUrl, $payload);

        if (!$resp->ok()) {
            Log::error('OpayService: cashier HTTP error', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['status' => false, 'message' => 'HTTP ' . $resp->status(), 'data' => $resp->json()];
        }

        $json       = $resp->json();
        $cashierUrl = $json['data']['cashierUrl'] ?? null;
        if (!$cashierUrl) {
            Log::error('OpayService: missing cashierUrl', ['response' => $json]);
            return ['status' => false, 'message' => $json['message'] ?? 'Unknown error', 'data' => $json];
        }

        return ['status' => true, 'data' => $json['data'], 'message' => $json['message'] ?? ''];
    }

    public function queryPaymentStatus(string $reference): array
    {
        $data     = ['country' => 'EG', 'reference' => $reference];

        // توقيع HMAC-SHA512 على JSON مرتّب كما في الدوك
        $payload   = json_encode($this->sortKeysRecursively($data), JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha512', $payload, $this->secretKey);

        $headers = [
            'Authorization' => 'Bearer ' . $signature, // هنا الـ Signature
            'MerchantId'    => $this->merchantId,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];

        $resp = Http::withHeaders($headers)
            ->withOptions([
                'timeout'         => 20,
                'connect_timeout' => 10,
                'curl'            => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
            ])
            ->withBody($payload, 'application/json')
            ->post($this->statusUrl);

        if (!$resp->ok()) {
            return ['ok' => false, 'message' => 'HTTP ' . $resp->status(), 'raw' => $resp->json()];
        }

        $json   = $resp->json();
        $status = $json['data']['status'] ?? null; // INITIAL|PENDING|SUCCESS|FAIL|CLOSE

        return [
            'ok'     => (($json['code'] ?? null) === '00000') && $status === 'SUCCESS',
            'status' => $status,
            'raw'    => $json,
        ];
    }

    private function sortKeysRecursively(array $arr): array
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->sortKeysRecursively($v);
            }
        }
        ksort($arr);
        return $arr;
    }
}
