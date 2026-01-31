<?php

// namespace App\Services\Payment;

// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use App\Models\Payment;
// use App\Models\User;
// use App\Models\UserContact;

// class XPayService
// {
//     protected string $baseUrl;
//     protected string $apiKey;
//     protected string $communityId;
//     protected int $variableAmountId;
//     protected bool $addFees;

//     public function __construct()
//     {
//         $this->baseUrl          = rtrim(config('payment.xpay_base', env('XPAY_BASE_URL', 'https://staging.xpay.app/api/v1')), '/');
//         $this->apiKey           = (string) config('payment.xpay_api_key', env('XPAY_API_KEY'));
//         $this->communityId      = (string) config('payment.xpay_community_id', env('XPAY_COMMUNITY_ID'));
//         $this->variableAmountId = (int) config('payment.xpay_variable_amount_id', env('XPAY_VARIABLE_AMOUNT_ID', 0));
//         $this->addFees          = (bool) config('payment.xpay_add_fees', env('XPAY_ADD_FEES', false));
//     }

//     public function initiateSession(Payment $payment, User $user, ?UserContact $contact): string
//     {
//         if (empty($this->apiKey) || empty($this->communityId) || empty($this->variableAmountId)) {
//             throw new \Exception('XPay: missing env (API_KEY / COMMUNITY_ID / VARIABLE_AMOUNT_ID)');
//         }

//         $amount = round((float) $payment->amount, 2);

//         if ($this->addFees) {
//             $prep = Http::withHeaders(['x-api-key' => $this->apiKey])
//                 ->post("{$this->baseUrl}/payments/prepare-amount/", [
//                     'community_id' => $this->communityId,
//                     'amount'       => $amount,
//                     'currency'     => 'EGP',
//                     'selected_payment_method' => 'card',
//                 ]);
//             if (!$prep->successful()) {
//                 Log::error('XPay prepare-amount failed', ['status' => $prep->status(), 'body' => $prep->body()]);
//                 throw new \Exception('XPay: prepare-amount failed');
//             }
//             $prepData = $prep->json('data') ?? [];
//             if (!empty($prepData['total_amount'])) {
//                 $amount = round((float) $prepData['total_amount'], 2);
//             }
//         }

//         $rawName = $contact?->name ?: ($user->name ?: 'Customer Name');
//         $name = preg_replace('/[^A-Za-z\s]/', '', $rawName);
//         $parts = preg_split('/\s+/', trim($name));
//         if (count($parts) < 2) {
//             $name = (count($parts) ? $parts[0] : 'User') . ' Test';
//         }

//         $rawPhone = $contact?->phone ?: ($user->phone ?? '+201000000000');
//         if (preg_match('/^0(10|11|12|15)\d{8}$/', $rawPhone)) {
//             $rawPhone = '+2' . $rawPhone;
//         }
//         $phone = str_starts_with($rawPhone, '+') ? $rawPhone : '+201000000000';

//         $email = $contact?->email ?: ($user->email ?? 'customer@example.com');

//         $resp = Http::withHeaders(['x-api-key' => $this->apiKey])
//             ->post("{$this->baseUrl}/payments/pay/variable-amount", [
//                 'community_id'       => $this->communityId,
//                 'variable_amount_id' => $this->variableAmountId,
//                 'amount'             => $amount,
//                 'original_amount'    => round((float) $payment->amount, 2),
//                 'currency'           => 'EGP',

//                 'pay_using'          => 'card',
//                 'billing_data'       => [
//                     'name'         => $name,
//                     'email'        => $email,
//                     'phone_number' => $phone,
//                 ],
//                 'language'           => 'en',
//             ]);

//         if (!$resp->successful()) {
//             Log::error('XPay pay/variable-amount failed', [
//                 'status' => $resp->status(),
//                 'body'   => $resp->body(),
//                 'sent'   => [
//                     'community_id' => $this->communityId,
//                     'variable_amount_id' => $this->variableAmountId,
//                     'amount' => $amount,
//                     'original_amount' => (float) $payment->amount,
//                     'name' => $name,
//                     'email' => $email,
//                     'phone' => $phone,
//                 ]
//             ]);
//             throw new \Exception('XPay: failed to create payment session. Check logs.');
//         }

//         // ... بعد ما تبعت POST على pay/variable-amount وتعدّي successful check

//         $data = $resp->json('data') ?? [];
//         $iframeUrl = $data['iframe_url'] ?? null;
//         $uuid      = $data['transaction_uuid'] ?? null;
//         $txId      = $data['transaction_id'] ?? null;      // جديد
//         $txStatus  = $data['transaction_status'] ?? null;  // جديد

//         if (!$iframeUrl || !$uuid) {
//             Log::error('XPay invalid response', ['data' => $data]);
//             throw new \Exception('XPay: invalid response (no iframe_url/uuid)');
//         }

//         // خزّن التفاصيل في الـpayload لنفس Payment
//         $payload = (array) ($payment->payload ?? []);
//         $payload['xpay'] = array_merge($payload['xpay'] ?? [], [
//             'transaction_uuid'   => $uuid,
//             'transaction_id'     => $txId,
//             'transaction_status' => $txStatus, // غالبًا pending
//             'iframe_url'         => $iframeUrl,
//         ]);

//         $payment->update([
//             'provider' => 'xpay',
//             'payload'  => $payload,
//         ]);

//         return $iframeUrl;
//     }

//     public function verify(string $transactionUuid): array
//     {
//         $resp = Http::withHeaders(['x-api-key' => $this->apiKey])
//             ->get("{$this->baseUrl}/communities/{$this->communityId}/transactions/{$transactionUuid}/");

//         if (!$resp->successful()) {
//             throw new \Exception('XPay: verification failed.');
//         }
//         return $resp->json('data') ?? [];
//     }
// }
