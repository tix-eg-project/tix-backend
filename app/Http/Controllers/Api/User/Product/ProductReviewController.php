<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return ApiResponseHelper::error(__('messages.products.not_found'), 404, null);
        }

        $perPage = min(max((int) request('per_page', 10), 1), 50);

        $paginator = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate($perPage);

        $items = $paginator->getCollection()->map(function (ProductReview $r) {
            return [
                'id' => $r->id,
                'rating' => (int) $r->rating,
                'review' => $r->review,
                'user_name' => $r->user->name ?? null,
                'created_at' => $r->created_at?->toIso8601String(),
            ];
        });

        $paginator->setCollection($items);

        return ApiResponseHelper::paginated($paginator, 'messages.product_reviews');
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return ApiResponseHelper::error(__('messages.products.not_found'), 404, null);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:5000',
        ]);

        $user = $request->user();

        ProductReview::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $data['rating'],
                'review' => $data['review'] ?? null,
            ]
        );

        return ApiResponseHelper::success('messages.default_success', null, 201);
    }
}
