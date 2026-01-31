<?php

// namespace App\Http\Controllers\Api\User\Payment;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Services\Payment\XPayService;

// class XPayRedirectController extends Controller
// {
//     public function redirect(Request $request, XPayService $xpay)
//     {
//         $ref = $request->query('ref');
//         abort_unless($ref, 404);

//         $payment = \App\Models\Payment::where('reference', $ref)
//             ->where('status', 'pending')->firstOrFail();

//         $order   = $payment->order()->with('user')->firstOrFail();
//         $user    = $order->user;
//         $contact = \App\Models\UserContact::where('user_id', $user->id)->first();

//         // ينشئ جلسة XPay ويرجّع لنا iframe_url + transaction_uuid
//         $iframe = $xpay->initiateSession($payment, $user, $contact);

//         // السيناريو الأصلي: نرمي المستخدم على بوابة XPay مباشرة
//         return redirect()->away($iframe);
//     }
// }
