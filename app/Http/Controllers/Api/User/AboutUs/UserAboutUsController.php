<?php

namespace App\Http\Controllers\Api\User\AboutUs;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;

class UserAboutUsController extends Controller
{
    /**
     * GET /api/banners
     * Display a list of banners with only id and image URL from Spatie Media Library.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $AboutUs = AboutUs::latest()
            ->get()
            ->map(fn($aboutus) => [
                'id'          => $aboutus->id,
                'title'       => $aboutus->title,
                'description'       => $aboutus->description,
                'image'       => asset($aboutus->image),
            ]);

        return ApiResponseHelper::success(
            __('messages.aboutus_list_retrieved'),
            $AboutUs
        );
    }
}
