<?php

namespace App\Http\Controllers\Api\User\Returns;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use App\Enums\ReturnStatusEnum;

class UserReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $userId   = (int) $request->user()->id;
        $perPage  = min(100, (int) $request->integer('per_page', 10));

        $status   = $request->filled('status') ? (int) $request->status : null;
        $showAll  = $request->boolean('show_all') || $request->get('show') === 'all';

        $vendorId = $request->integer('vendor_id');
        $from     = $request->date('from');
        $to       = $request->date('to');
        $search   = trim((string) $request->get('search', ''));
        $sortBy   = in_array($request->get('sort_by'), ['id','created_at','approved_at'], true) ? $request->get('sort_by') : 'id';
        $sortDir  = strtolower($request->get('sort', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = ReturnRequest::query()
            ->where('user_id', $userId)
            ->with([
                'order:id,user_id,total,delivered_at,created_at',
                'orderItem:id,order_id,product_id,product_variant_item_id,product_name,product_image,price_before,price_after,quantity,vendor_id',
                'orderItem.variantItem:id,product_id,selections,price,quantity,is_active',
                'vendor:id,name',
            ])

            // ✅ الافتراضي: المفتوح + المقبول (سليم/تالف)
            ->when(is_null($status) && !$showAll, function ($q) {
                $q->whereIn('status', [
                    ReturnStatusEnum::PendingReview->value,
                    ReturnStatusEnum::UnderReturn->value,
                    ReturnStatusEnum::ReceivedGood->value,
                    ReturnStatusEnum::ReceivedDefective->value,
                ]);
            })

            // فلترة حالة معينة لو مبعوتة
            ->when(!is_null($status), fn($q) => $q->where('status', $status))
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    if (ctype_digit($search)) {
                        $id = (int) $search;
                        $qq->orWhere('id', $id)
                           ->orWhere('order_id', $id)
                           ->orWhere('order_item_id', $id);
                    }
                    $qq->orWhereHas('orderItem', fn($iq) => $iq->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->orderBy($sortBy, $sortDir);

        $returns = $query->paginate($perPage)->through(function ($r) {
            $item    = $r->orderItem;
            $variant = $item?->variantItem;

            return [
                'id'           => (int) $r->id,
                'status_label' => $r->status_label ?? null,
                'reason_label' => $r->reason_label ?? null,
                'quantity'     => (int) $r->quantity,

                'refunds' => [
                    'subtotal' => (float) $r->refund_subtotal,
                    'shipping' => (float) $r->refund_shipping,
                    'total'    => (float) $r->refund_total,
                ],

                'created_at'  => optional($r->created_at)?->toDateTimeString(),
                'approved_at' => optional($r->approved_at)?->toDateTimeString(),
                'received_at' => optional($r->received_at)?->toDateTimeString(),
                'refunded_at' => optional($r->refunded_at)?->toDateTimeString(),

                'order' => [
                    'id'           => (int) $r->order_id,
                    'total'        => (float) ($r->order?->total ?? 0),
                    'delivered_at' => optional($r->order?->delivered_at)?->toDateTimeString(),
                ],

                'item' => $item ? [
                    'id'               => (int) $item->id,
                    'product_id'       => (int) $item->product_id,
                    'product_name'     => $item->product_name,
                    'product_image'    => $item->product_image,
                    'price_before'     => (float) $item->price_before,
                    'price_after'      => (float) $item->price_after,
                    'ordered_quantity' => (int) $item->quantity,
                ] : null,

                'vendor' => $r->vendor ? [
                    'id'   => (int) $r->vendor->id,
                    'name' => $r->vendor->name,
                ] : null,
            ];
        });

        return ApiResponseHelper::paginated($returns, 'messages.return_requests.list');
    }
}
