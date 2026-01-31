<?php

namespace App\Http\Controllers\Api\User\Order;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Order\UserOrderService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    public function __construct(protected UserOrderService $service) {}

    public function index()
    {
        try {
            $data = $this->service->list();
            return ApiResponseHelper::paginated($data, 'messages.orders.list',);
        } catch (Throwable $e) {
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }

    public function show(int $id)
    {
        try {
            $data = $this->service->show($id);
            return ApiResponseHelper::success('messages.orders.show', $data);
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::error(__('messages.orders.not_found'), 404);
        } catch (Throwable $e) {
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->destroy($id);
            return ApiResponseHelper::success('messages.orders.deleted');
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::error(__('messages.orders.not_found'), 404);
        } catch (Throwable $e) {
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }

    public function clear()
    {
        try {
            $this->service->clear();
            return ApiResponseHelper::success('messages.orders.cleared');
        } catch (Throwable $e) {
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }
}
