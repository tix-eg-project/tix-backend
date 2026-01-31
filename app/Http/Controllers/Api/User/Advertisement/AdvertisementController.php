<?php

namespace App\Http\Controllers\Api\User\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;

class AdvertisementController extends Controller
{
    /**
     * GET /api/advertisements
     * Display a list of advertisements with id and image URL.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $advertisements = Advertisement::latest()
            ->get()
            ->map(fn($advertisement) => [
                'id'    => $advertisement->id,
                'image' => asset($advertisement->image),
            ]);

        return ApiResponseHelper::success(
            __('messages.advertisements_list_retrieved'),
            $advertisements
        );
    }
}
