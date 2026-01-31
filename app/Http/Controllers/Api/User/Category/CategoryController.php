<?php

namespace App\Http\Controllers\Api\User\Category;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET /api/banners
     * Display a list of banners with only id and image URL from Spatie Media Library.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::latest()
            ->get()
            ->map(fn($category) => [
                'id'          => $category->id,
                'name'       => $category->name,
                'image'       => asset($category->image),
            ]);

        return ApiResponseHelper::success(
            __('messages.category_list_retrieved'),
            $categories
        );
    }
}
