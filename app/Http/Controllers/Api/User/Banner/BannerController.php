<?php

namespace App\Http\Controllers\Api\User\Banner;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Enums\ImageEnum;

class BannerController extends Controller
{
    /**
     * GET /api/banners
     * Display a list of banners with only id and image URL from Spatie Media Library.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $banners = Banner::latest()
            ->get()
            ->map(fn($banner) => [
                'id'          => $banner->id,
                'title'       => $banner->title,
                'description' => $banner->description,
                'image'       => asset($banner->image),
                'vendor_id'   => $banner->vendor_id,
            ]);

        return ApiResponseHelper::success(
            __('messages.banners_list_retrieved'),
            $banners
        );
    }
}
