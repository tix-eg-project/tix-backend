<?php

namespace App\Http\Controllers\Api\User\Cart;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Faker\Provider\ar_EG\Payment;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentmethod = PaymentMethod::select('id', 'name')
            ->get()
            ->map(function ($payment) {
                return [
                    'id'   => $payment->id,
                    'name' => $payment->name,
                ];
            });

        return ApiResponseHelper::success('messages.PaymentMethod_list', $paymentmethod);
    }
}
