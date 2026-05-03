<?php

// namespace App\Http\Controllers\Api\User\Payment;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB;
// use App\Services\Payment\XPayService;
// use App\Models\Payment;

// class XPayCallbackController extends Controller
// {
//     public function callback(Request $request, XPayService $xpay)
//     {
//         Log::info('✅ XPay callback received', [
//             'query' => $request->query(),
//             'json'  => $request->json()->all(),
//             'form'  => $request->post(),
//         ]);

//         // XPay أحيانًا تبعت uuid في مسارات مختلفة
//         $uuid = $request->input('transaction_uuid')
//             ?? data_get($request->all(), 'data.transaction_uuid')
//             ?? data_get($request->all(), 'transaction.transaction_uuid');

//         if (!$uuid) {
//             Log::warning('XPay callback: missing transaction_uuid');

//             // رجّع صفحة فشل لأننا مش قادرين نتأكد من العملية
//             return response()->view('payment.result', [
//                 'success'        => false,
//                 'order_id'       => null,
//                 'transaction_id' => null,
//             ], 400);
//         }

//         // لقّي الـPayment من الـ uuid المحفوظ في payload
//         /** @var Payment|null $payment */
//         $payment = Payment::where('provider', 'xpay')
//             ->where('payload->xpay->transaction_uuid', $uuid)
//             ->first();

//         if (!$payment) {
//             Log::warning('XPay callback: payment not found for uuid ' . $uuid);

//             return response()->view('payment.result', [
//                 'success'        => false,
//                 'order_id'       => null,
//                 'transaction_id' => $uuid,
//             ], 404);
//         }

//         // Verify من XPay لتثبيت النتيجة
//         try {
//             $v = $xpay->verify($uuid);
//         } catch (\Throwable $e) {
//             Log::error('XPay verify error: ' . $e->getMessage());

//             // في حالة فشل التحقق: نعرض Pending بدل ما نقول فشل نهائي
//             return response()->view('payment.result', [
//                 'success'        => null, // ⏳ Pending
//                 'order_id'       => optional($payment->order)->id,
//                 'transaction_id' => $uuid,
//             ], 502);
//         }

//         $status = strtolower($v['status'] ?? '');
//         $paid   = $status === 'successful';
//         $failed = $status === 'failed';

//         DB::transaction(function () use ($payment, $paid, $failed, $v) {
//             if ($payment->status === 'pending') {
//                 $payment->update([
//                     'status' => $paid ? 'successful' : ($failed ? 'failed' : 'pending'),
//                     'payload->xpay->verify_cb' => $v,
//                 ]);
//                 if ($order = $payment->order) {
//                     if ($paid) {
//                         $order->status         = 'paid';
//                         $order->payment_status = 'paid';
//                     } elseif ($failed) {
//                         $order->status         = 'payment_failed';
//                         $order->payment_status = 'failed';
//                     }
//                     $order->save();
//                 }
//             }
//         });

//         // نجاح الدفع: نفّذ المؤجّل (خصم مخزون/كوبون/سلة/إشعارات) بأمان بعد الكمِت
//         if ($paid && ($order = $payment->order)) {
//             DB::afterCommit(function () use ($order) {
//                 app(\App\Services\Order\CheckoutService::class)->finalizePaidOrder($order);
//             });
//         }

//         // رجّع صفحة الـ Blade بنفس الـ UX بتاعك
//         return response()->view('payment.result', [
//             'success'        => $paid ? true : ($failed ? false : null), // true/false/ null=Pending
//             'order_id'       => optional($payment->order)->id,
//             'transaction_id' => $uuid,
//         ]);
//     }
// }
