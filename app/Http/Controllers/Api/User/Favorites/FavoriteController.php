<?php

namespace App\Http\Controllers\Api\User\Favorites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Favorites\ToggleFavoriteRequest;
use App\Services\FavoriteService;
use App\Helpers\ApiResponseHelper;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $service) {}

    public function toggle(ToggleFavoriteRequest $request)
    {
        $result = $this->service->toggle($request->product_id);
        return ApiResponseHelper::success(__('messages.updated_successfully'), $result);
    }

    public function index()
    {
        $data = $this->service->list();
        return ApiResponseHelper::success(__('messages.retrieved_successfully'), $data);
    }

    public function destroy($productId)
    {
        $this->service->remove($productId);
        return ApiResponseHelper::success(__('messages.deleted_successfully'));
    }

    public function clear()
    {
        $this->service->clearAll();
        return ApiResponseHelper::success(__('messages.deleted_successfully'));
    }
}
