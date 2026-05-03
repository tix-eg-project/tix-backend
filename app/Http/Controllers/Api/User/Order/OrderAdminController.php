<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Order\AdminOrderService;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function __construct(protected AdminOrderService $service) {}

    public function updateStatus(Request $r, int $id)
    {
        $r->validate(['status' => ['required', 'string', 'max:50']]);
        $data = $this->service->updateStatus($id, $r->string('status')->toString());
        return ApiResponseHelper::success('messages.orders.status_updated', $data);
    }

    public function setDeliveredAt(Request $r, int $id)
    {
        $r->validate(['delivered_at' => ['required', 'date']]);
        $data = $this->service->setDeliveredAt($id, $r->input('delivered_at'));
        return ApiResponseHelper::success('messages.orders.delivered_at_set', $data);
    }
}
