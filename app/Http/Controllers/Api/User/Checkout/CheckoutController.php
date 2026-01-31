<?php

namespace App\Http\Controllers\Api\User\Checkout;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Order\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function processCheckout(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_method_id'   => 'nullable|integer|exists:payment_methods,id|required_without:payment_method_name',
                'payment_method_name' => 'nullable|string|required_without:payment_method_id',
                'shipping_zone_id'    => 'nullable|integer|exists:shipping_zones,id',
            ]);

            $result = $this->checkoutService->processCheckout(
                paymentMethodId: $validated['payment_method_id'] ?? null,
                paymentMethodName: $validated['payment_method_name'] ?? null,
                shippingZoneId: $validated['shipping_zone_id'] ?? null
            );

            $responseData = [
                'order_id'         => $result['order']->id,
                'order_status'     => $result['order']->status,
                'payment_status'   => $result['order']->payment_status,
                'total'            => $result['order']->total,
                'requires_redirect' => $result['requires_redirect'],
                'redirect_url'     => $result['redirect_url'] ?? null,
            ];

            if (!empty($result['payment'])) {
                $responseData['payment_reference'] = $result['payment']['reference'];
            }

            return ApiResponseHelper::success(
                $result['requires_redirect']
                    ? __('messages.checkout.pending_payment')
                    : __('messages.checkout.success'),
                $responseData
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseHelper::error(__('messages.payment.method_required'), 422, $e->errors());
        } catch (\DomainException $e) {
            return ApiResponseHelper::error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            report($e);
            $msg = config('app.debug') ? $e->getMessage() : __('messages.checkout.error');
            return ApiResponseHelper::error($msg, 500);
        }
    }
}
