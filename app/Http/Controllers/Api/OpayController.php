<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpayController extends Controller
{
    private OpayService $opayService;

    public function __construct(OpayService $opayService)
    {
        $this->opayService = $opayService;
    }

    public function initializePayment(array $data): array
    {
        $response = $this->opayService->initiateCashierPayment($data);

        if (!$response['status'] || empty($response['data']['cashierUrl'])) {
            return [
                'status'  => false,
                'message' => $response['message'] ?? 'Unknown error from OPay',
                'data'    => $response['data'] ?? []
            ];
        }

        return [
            'status'      => true,
            'payment_url' => $response['data']['cashierUrl'],
            'reference'   => $response['data']['reference'] ?? $data['reference'] ?? null,
        ];
    }

    public function webhook(Request $request)
    {
        $input   = $request->all();
        $payload = $input['payload'] ?? null;
        $sha512  = $input['sha512']  ?? null;

        if (!$payload || !$sha512) {
            Log::warning('Opay webhook: missing payload or signature', ['input' => $input]);
            return response()->json(['ok' => false], 200);
        }

        try {
            $refundedFlag = (isset($payload['refunded']) && $payload['refunded'] === true) ? 't' : 'f';

            $signContent = sprintf(
                '{Amount:"%s",Currency:"%s",Reference:"%s",Refunded:%s,Status:"%s",Timestamp:"%s",Token:"%s",TransactionID:"%s"}',
                $payload['amount'] ?? '',
                $payload['currency'] ?? '',
                $payload['reference'] ?? '',
                $refundedFlag,
                $payload['status'] ?? '',
                $payload['timestamp'] ?? '',
                $payload['token'] ?? '',
                $payload['transactionId'] ?? ''
            );

            $calc  = hash_hmac('sha3-512', $signContent, config('opay.secret_key'));
            $valid = hash_equals(strtolower($sha512), strtolower($calc));
        } catch (\Throwable $e) {
            Log::error('Opay webhook: signature build error', ['e' => $e->getMessage()]);
            $valid = false;
        }

        if (!$valid) {
            Log::warning('Opay webhook: invalid signature', ['input' => $input]);
            return response()->json(['ok' => true], 200);
        }

        $ref = $payload['reference'] ?? null;
        if ($ref) {
            $check = $this->opayService->queryPaymentStatus($ref);
            if (($check['ok'] ?? false) === true) {
                try {
                    DB::transaction(function () use ($ref) {
                        // أحدث Payment لهذا المرجع
                        $payment = \App\Models\Payment::where('reference', $ref)->latest('id')->first();
                        if ($payment && $payment->status === 'pending') {
                            $payment->update(['status' => 'success']);
                        }

                        // اعتمد على علاقة الـ Payment -> Order
                        $order = $payment?->order ?? \App\Models\Order::where('order_number', $ref)->first();
                        if ($order && !$order->is_paid) {
                            $order->update([
                                'is_paid'        => true,
                                'status'         => 'paid',
                                'payment_status' => 'paid',
                                'payment_id'     => $ref,
                            ]);

                            app(\App\Http\Controllers\Api\PaymentCallbackController::class)
                                ->finalizeOrder($order->id);
                        }
                    });
                } catch (\Throwable $e) {
                    Log::error('Opay webhook finalize error', ['e' => $e->getMessage()]);
                }
            } else {
                Log::info('Opay webhook: status not SUCCESS', ['ref' => $ref, 'status' => $check['status'] ?? null]);
            }
        }

        return response()->json(['ok' => true], 200);
    }
}
