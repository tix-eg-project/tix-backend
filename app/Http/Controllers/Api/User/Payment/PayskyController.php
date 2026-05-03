<?php

// namespace App\Http\Controllers\Api\User\Payment; // <- Api بحروف كبيرة

// use App\Http\Controllers\Controller;
// use App\Models\Order;
// use App\Models\Payment;
// use App\Services\Payment\PayskyGateway;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

// class PayskyController extends Controller
// {
//     // ده اللي هيضربه الـ PaySky كـ server-to-server
//     public function callback(Request $request)
//     {
//         try {
//             $pg = app(PayskyGateway::class);
//             $ver = $pg->verifyCallback($request->all()); // تأكد أن الميثود موجودة في الـ Gateway

//             // جبنا الأوردر
//             $orderId = $ver['order_id'] ?? null;
//             $order   = $orderId ? Order::find($orderId) : null;

//             if ($order) {
//                 // خزّن/حدّث Payment مرتبط
//                 Payment::updateOrCreate(
//                     ['order_id' => $order->id],
//                     [
//                         'provider'  => 'paysky',
//                         'reference' => $ver['provider_id'] ?? null,
//                         'amount'    => $ver['amount'] ?? $order->total,
//                         'currency'  => config('services.paysky.currency', 'EGP'),
//                         'status'    => $ver['success'] ? 'paid' : 'failed',
//                         'payload'   => $ver['raw'] ?? $request->all(),
//                     ]
//                 );

//                 // حدّث حالة الأوردر
//                 $order->payment_status = $ver['success'] ? 'paid' : 'failed';
//                 if ($ver['success']) {
//                     $order->status = 'paid'; // أو الحالة المعتمدة عندك
//                 }
//                 $order->save();
//             }

//             return response()->json(['ok' => true], 200);
//         } catch (\Throwable $e) {
//             Log::error('Paysky callback error', ['ex' => $e->getMessage()]);
//             return response()->json(['ok' => false], 200); // معظم البوابات تحب 200
//         }
//     }
// }
