<?php

namespace App\Http\Controllers\Api\User\ProductReviews;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductReviews\ProductReviewRequest;
use App\Services\ProductReviewService;
use Illuminate\Http\JsonResponse;

class ProductReviewController extends Controller
{
    protected ProductReviewService $reviewService;

    public function __construct(ProductReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    // =========================================================
    // POST /products/{id}/reviews   → إضافة تقييم
    // =========================================================
    public function store(ProductReviewRequest $request, int $productId): JsonResponse
    {
        $userId = $request->user()->id;

        // منع التقييم المكرر
        if ($this->reviewService->userAlreadyReviewed($productId, $userId)) {
            return ApiResponseHelper::error('messages.review.already_reviewed', 422);
        }

        // دمج الـ product_id من الـ route في الـ data
        $data = array_merge($request->validated(), ['product_id' => $productId]);

        $review = $this->reviewService->store($data, $userId, $request->file('image'));

        return ApiResponseHelper::success('messages.review.added_success', $review);
    }

    // =========================================================
    // GET /products/{id}/reviews    → قائمة التقييمات
    // =========================================================
    public function index(int $productId): JsonResponse
    {
        $data = $this->reviewService->getReviewsForProduct($productId);

        return ApiResponseHelper::success('messages.review.list_success', $data);
    }
}
