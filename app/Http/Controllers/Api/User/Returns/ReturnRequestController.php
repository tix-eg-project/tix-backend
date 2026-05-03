<?php

namespace App\Http\Controllers\Api\User\Returns;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Return\StoreReturnRequest;
use App\Models\Order;
use App\Services\Returns\ReturnRequestService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    // POST /api/orders/{order}/returns  أو  /api/orders/{id}/returns
    public function store(StoreReturnRequest $request, ReturnRequestService $service)
    {
        $order = $this->resolveOrderFromRoute($request);

        $returnRequest = $service->createForOrderItem($order, $request->payload());

        return ApiResponseHelper::success(
            'messages.request_sent_admin',

        );
    }

    private function resolveOrderFromRoute(Request $request): Order
    {
        $param = $request->route('order') ?? $request->route('id') ?? $request->route('order_id');

        if ($param instanceof Order) {
            $order = $param;
        } else {
            $order = Order::query()->findOrFail((int) $param);
        }

        if ($request->user() && (int)$order->user_id !== (int)$request->user()->id) {
            abort(403);
        }

        return $order;
    }
}
