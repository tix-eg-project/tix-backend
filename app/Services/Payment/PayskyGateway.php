<?php

// namespace App\Services\Payment;

// use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Http;

// class PayskyGateway
// {
//     /**
//      * يكوّن جلسة دفع ويرجع رابط التحويل + مرجع المزوّد + الاستجابة الخام.
//      * يتعامل مع حقول متعددة الأسماء حتى لو الوثيقة تستخدم مفاتيح مختلفة.
//      */
//     public function createPaymentSession(array $data): array
//     {
//         $endpoint   = (string) config('services.paysky.payform_endpoint');
//         $merchantId = (string) config('services.paysky.merchant_id');
//         $terminalId = (string) config('services.paysky.terminal_id');
//         $currency   = (string) config('services.paysky.currency', 'EGP');
//         $returnUrl  = (string) config('services.paysky.return_url');
//         $callbackUrl = (string) config('services.paysky.callback_url');
//         $secret     = (string) config('services.paysky.secret');
//         $signAlgo   = (string) config('services.paysky.sign_algo', 'hmac-sha256'); // أو none

//         // تأكيد البيانات الأساسية
//         if (! $endpoint || ! $merchantId || ! $terminalId) {
//             throw new \RuntimeException('Paysky config missing: endpoint/merchant_id/terminal_id');
//         }
//         if (empty($data['order_id']) || empty($data['amount'])) {
//             throw new \InvalidArgumentException('order_id and amount are required');
//         }

//         // صياغة الحمولة (payload)
//         $payload = [
//             'merchantId' => $merchantId,
//             'terminalId' => $terminalId,
//             'orderId'    => (string) $data['order_id'],
//             'amount'     => number_format((float)$data['amount'], 2, '.', ''),
//             'currency'   => $currency,
//             'returnUrl'  => $returnUrl,
//             'callbackUrl' => $callbackUrl,
//             // معلومات العميل (اختيارية)
//             'customer'   => Arr::only(($data['customer'] ?? []), ['name', 'email', 'phone']),
//         ];

//         // توقيع اختياري (لو الوثيقة تتطلب HMAC)
//         if ($secret && $signAlgo !== 'none') {
//             $payload['signature'] = $this->signPayload($payload, $secret, $signAlgo);
//             $payload['signatureAlgo'] = $signAlgo;
//         }

//         // طلب HTTP مع إعادة المحاولة والمهلة
//         $resp = Http::asJson()
//             ->timeout((int) config('services.paysky.timeout', 20))
//             ->retry(2, 300) // محاولتان تأخير 300ms
//             ->post($endpoint, $payload);

//         if (!$resp->ok()) {
//             Log::error('Paysky createPaymentSession: non-200', [
//                 'status' => $resp->status(),
//                 'body'   => $resp->body(),
//             ]);
//             $resp->throw();
//         }

//         $json = $resp->json() ?? [];

//         // التقاط مفاتيح متعددة لعنوان التحويل ومعرّف العملية
//         $redirectUrl = $json['payFormUrl']
//             ?? $json['redirectUrl']
//             ?? $json['iframe_url']
//             ?? $json['url']
//             ?? null;

//         $providerRef = $json['transactionId']
//             ?? $json['paymentId']
//             ?? $json['id']
//             ?? null;

//         if (!$redirectUrl) {
//             // لو ما رجع رابط تحويل، اعتبره فشل تكوين جلسة
//             Log::error('Paysky createPaymentSession: missing redirect URL', ['json' => $json]);
//             throw new \RuntimeException('Paysky: missing redirect URL in response');
//         }

//         return [
//             'redirect_url' => $redirectUrl,
//             'provider_ref' => $providerRef,
//             'raw'          => $json,
//         ];
//     }

//     /**
//      * التحقّق من الـ Webhook/Callback (توقيع اختياري).
//      * يدعم مفاتيح حالة متعددة ويُرجِع بنية موحدة.
//      */
//     public function verifyWebhook(array $payload, array $headers = []): array
//     {
//         $secret   = (string) config('services.paysky.secret');
//         $signAlgo = (string) config('services.paysky.sign_algo', 'hmac-sha256'); // أو none

//         $isValidSignature = true;
//         if ($secret && $signAlgo !== 'none') {
//             // نحاول نجمع التوقيع من الهيدر أو من الحقل داخل الـ payload
//             $provided = $headers['X-Signature']
//                 ?? $headers['x-signature']
//                 ?? $payload['signature']
//                 ?? null;

//             $expected = $this->signPayload($payload, $secret, $signAlgo);
//             $isValidSignature = is_string($provided) && hash_equals($expected, $provided);
//         }

//         // استخراج الحقول الشائعة
//         $status  = strtolower((string) ($payload['status'] ?? $payload['paymentStatus'] ?? ''));
//         $success = $this->mapStatusToSuccess($status);

//         return [
//             'valid'       => $isValidSignature,
//             'success'     => $success,
//             'provider_id' => $payload['transactionId'] ?? $payload['paymentId'] ?? $payload['id'] ?? null,
//             'order_id'    => $payload['orderId'] ?? $payload['merchantOrderId'] ?? null,
//             'amount'      => $payload['amount'] ?? $payload['total'] ?? null,
//             'raw'         => $payload,
//         ];
//     }

//     /**
//      * alias للتماشي مع استدعاءات قديمة في الكنترولر.
//      */
//     public function verifyCallback(array $payload, array $headers = []): array
//     {
//         return $this->verifyWebhook($payload, $headers);
//     }

//     /**
//      * توقيع HMAC بسيط على JSON مُرتَّب (stable) لتجنّب اختلاف ترتيب المفاتيح.
//      * يمكنك تغييره لاحقًا إذا كانت الوثيقة تطلب صيغة محدّدة للتوقيع.
//      */
//     protected function signPayload(array $payload, string $secret, string $algo = 'hmac-sha256'): string
//     {
//         // إزالة حقول لا ينبغي تضمينها في التوقيع
//         unset($payload['signature'], $payload['signatureAlgo']);

//         // ترتيب المفاتيح أبجدياً لثبات التمثيل
//         ksort($payload);

//         // تمثيل JSON ثابت بدون مسافات
//         $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

//         // اختيار الخوارزمية
//         $hashAlgo = match (strtolower($algo)) {
//             'hmac-sha512' => 'sha512',
//             default       => 'sha256',
//         };

//         return hash_hmac($hashAlgo, $json, $secret);
//     }

//     /**
//      * تحويل حالة البوابة إلى نجاح/فشل.
//      */
//     protected function mapStatusToSuccess(string $status): bool
//     {
//         if ($status === '') return false;

//         $successValues = ['paid', 'success', 'approved', 'succeeded', 'completed', 'ok'];
//         $failValues    = ['failed', 'declined', 'canceled', 'cancelled', 'error', 'expired'];

//         if (in_array($status, $successValues, true)) return true;
//         if (in_array($status, $failValues, true))    return false;

//         // حالات غير معروفة تعامل كفشل حتى يثبت العكس
//         return false;
//     }
// }
