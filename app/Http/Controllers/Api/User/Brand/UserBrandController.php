<?php

namespace App\Http\Controllers\Api\User\Brand;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class UserBrandController extends Controller
{
    /**
     * GET /api/banners
     * Display a list of banners with only id and image URL from Spatie Media Library.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = Brand::latest()
            ->get()
            ->map(fn($brand) => [
                'id'          => $brand->id,
                'name'       => $brand->name,
                //'image'       => asset($category->image),
            ]);

        return ApiResponseHelper::success(
            __('messages.brand_list_retrieved'),
            $brands
        );
    }
}
