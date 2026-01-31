<?php

// namespace App\Http\Controllers\Api\User\Payment;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Services\Payment\XPayService;
// use App\Models\Payment;

// class XPayStatusController extends Controller
// {
//     // GET /api/payments/xpay/status?ref=PAY-...&verify=1
//     public function status(Request $request, XPayService $xpay)
//     {
//         $ref = $request->query('ref');
//         $doVerify = (bool) $request->boolean('verify', true);

//         if (!$ref) {
//             return response()->json(['status' => false, 'message' => 'payment.reference_required'], 400);
//         }

//         $payment = Payment::where('reference', $ref)->first();
//         if (!$payment) {
//             return response()->json(['status' => false, 'message' => 'payment.not_found'], 404);
//         }

//         $uuid = $payment->payload['xpay']['transaction_uuid'] ?? null;

//         if ($payment->status === 'pending' && $doVerify && $uuid) {
//             try {
//                 $v = $xpay->verify($uuid);
//                 $s = strtolower($v['status'] ?? '');
//                 if ($s === 'successful' || $s === 'failed') {
//                     $paid = $s === 'successful';
//                     $payment->update([
//                         'status' => $paid ? 'successful' : 'failed',
//                         'payload->xpay->verify_poll' => $v,
//                     ]);
//                     if ($order = $payment->order) {
//                         $order->status = $paid ? 'paid' : 'payment_failed';
//                         $order->payment_status = $paid ? 'paid' : 'failed';
//                         $order->save();
//                     }
//                 }
//             } catch (\Throwable $e) {
//                 Log::error('XPay status verify error: ' . $e->getMessage());
//             }
//         }

//         $order = $payment->order;
//         return response()->json([
//             'status'  => $payment->status === 'successful',
//             'message' => match ($payment->status) {
//                 'successful' => 'payment.success',
//                 'failed'     => 'payment.failed',
//                 default      => 'payment.pending',
//             },
//             'data' => [
//                 'order_id'         => $order?->id,
//                 'order_status'     => $order?->status,
//                 'payment_status'   => $payment->status,
//                 'transaction_uuid' => $uuid,
//                 'reference'        => $payment->reference,
//             ],
//         ]);
//     }
// }
