<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OpayService;
use App\Services\Order\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        $gateway   = strtolower($request->query('gateway'));
        $reference = $request->query('reference') ?? $request->query('tx_ref');

        try {
            switch ($gateway) {
                case 'opay':
                    return $this->handleOpay($reference);
                default:
                    return $this->redirectToClient(null, false);
            }
        } catch (\Throwable $e) {
            Log::error('Unified Payment Callback Error', ['error' => $e->getMessage()]);
            return $this->redirectToClient(null, false);
        }
    }

    protected function handleOpay(?string $reference)
    {
        if (!$reference) {
            return $this->redirectToClient(null, false);
        }

        try {
            $result = app(OpayService::class)->queryPaymentStatus($reference);
        } catch (\Throwable $e) {
            Log::error('OPay verify error', ['e' => $e->getMessage(), 'ref' => $reference]);
            return $this->redirectToClient(null, false);
        }

        if (!($result['ok'] ?? false)) {
            Log::info('OPay status not SUCCESS', [
                'ref'    => $reference,
                'status' => $result['status'] ?? null,
                'raw'    => $result['raw'] ?? null
            ]);
            return $this->redirectToClient(null, false);
        }

        // أحدث Payment لهذا المرجع
        $payment = Payment::where('reference', $reference)->latest('id')->first();
        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'success']);
        }

        // الأفضل الاعتماد على علاقة الـ Payment -> order
        $order = $payment?->order ?? Order::where('order_number', $reference)->first();
        if (!$order) {
            return $this->redirectToClient(null, false);
        }

        if (!$order->is_paid) {
            $order->update([
                'is_paid'        => true,
                'status'         => 'paid',
                'payment_status' => 'paid',
                'payment_id'     => $reference,
            ]);

            if (!$this->finalizeOrder($order->id)) {
                return $this->redirectToClient($order, false);
            }
        }

        return $this->redirectToClient($order, true);
    }

    protected function finalizeOrder(int $order_id): bool
    {
        try {
            $order = Order::findOrFail($order_id);
            app(CheckoutService::class)->finalizePaidOrder($order);
            return true;
        } catch (\Throwable $e) {
            Log::error('Finalize Order Error', ['error' => $e->getMessage(), 'order_id' => $order_id]);
            return false;
        }
    }

    protected function redirectToClient(?Order $order, bool $success)
    {
        $id  = $order?->id ?? '';
        $url = 'https://updates.medicaoverseas.com/api/navigat'
            . '?id=' . $id
            . '&success=' . ($success ? 'true' : 'false');

        return view('payment', [
            'order' => $order,
            'url'   => $url,
        ]);
    }
}
