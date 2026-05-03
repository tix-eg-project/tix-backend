<?php

// namespace App\Http\Controllers\Api\User\Payment;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Services\Payment\XPayService;
// use App\Models\Payment;

// class XPayReturnApiController extends Controller
// {

//     public function return(Request $request, XPayService $xpay)
//     {
//         $uuid = $request->query('transaction_uuid') ?? $request->query('uuid');
//         $ref  = $request->query('ref');

//         $payment = null;

//         if ($uuid) {
//             $payment = Payment::where('provider', 'xpay')
//                 ->where('payload->xpay->transaction_uuid', $uuid)
//                 ->first();
//         }

//         if (!$payment && $ref) {
//             $payment = Payment::where('reference', $ref)->first();
//             $uuid = $payment?->payload['xpay']['transaction_uuid'] ?? $uuid;
//         }

//         if (!$payment) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'payment.not_found',
//                 'data'    => null,
//             ], 404);
//         }


//         if ($payment->status !== 'pending') {
//             $order = $payment->order;
//             return response()->json([
//                 'status'  => $payment->status === 'successful',
//                 'message' => $payment->status === 'successful' ? 'payment.success' : 'payment.failed',
//                 'data'    => [
//                     'order_id'         => $order?->id,
//                     'order_status'     => $order?->status,
//                     'payment_status'   => $payment->status,
//                     'transaction_uuid' => $uuid,
//                     'reference'        => $payment->reference,
//                 ],
//             ]);
//         }

//         if (!$uuid) {

//             return response()->json([
//                 'status'  => false,
//                 'message' => 'payment.pending_no_uuid',
//                 'data'    => [
//                     'order_id'       => optional($payment->order)->id,
//                     'payment_status' => 'pending',
//                     'reference'      => $payment->reference,
//                 ],
//             ]);
//         }

//         try {

//             $v = $xpay->verify($uuid);
//             $paid = strtolower($v['status'] ?? '') === 'successful';

//             $payment->update([
//                 'status' => $paid ? 'successful' : 'failed',
//                 'payload->xpay->verify_return' => $v,
//             ]);

//             $order = $payment->order;
//             if ($order) {
//                 $order->status         = $paid ? 'paid' : 'payment_failed';
//                 $order->payment_status = $paid ? 'paid' : 'failed';
//                 $order->save();
//             }

//             return response()->json([
//                 'status'  => $paid,
//                 'message' => $paid ? 'payment.success' : 'payment.failed',
//                 'data'    => [
//                     'order_id'         => $order?->id,
//                     'order_status'     => $order?->status,
//                     'payment_status'   => $payment->status,
//                     'transaction_uuid' => $uuid,
//                     'reference'        => $payment->reference,
//                 ],
//             ]);
//         } catch (\Throwable $e) {
//             Log::error('XPay return verify error: ' . $e->getMessage());
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'payment.verify_error',
//                 'data'    => [
//                     'order_id'       => optional($payment->order)->id,
//                     'payment_status' => 'pending',
//                     'reference'      => $payment->reference,
//                 ],
//             ], 502);
//         }
//     }
// }
