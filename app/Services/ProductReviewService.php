<?php

namespace App\Services;

use App\Helpers\ImageManger;
use App\Models\Product;
use App\Models\ProductReview;

class ProductReviewService
{
    protected ImageManger $imageManger;

    public function __construct(ImageManger $imageManger)
    {
        $this->imageManger = $imageManger;
    }

    // =========================================================
    // إضافة تقييم جديد
    // =========================================================
    public function store(array $data, int $userId, $imageFile = null): ProductReview
    {
        $imagePath = null;

        if ($imageFile) {
            $imagePath = $this->imageManger->uploadImage('reviews', $imageFile);
        }

        return ProductReview::create([
            'user_id'    => $userId,
            'product_id' => $data['product_id'],
            'comment'    => $data['comment'],
            'rating'     => $data['rating'],
            'image'      => $imagePath,
        ]);
    }

    // =========================================================
    // جلب تعليقات منتج + متوسط التقييم
    // =========================================================
    public function getReviewsForProduct(int $productId): array
    {
        $product = Product::find($productId);

        if (!$product) {
            return [];
        }

        $reviews = ProductReview::with('user')
            ->where('product_id', $productId)
            ->where('is_visible', true)
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating') ?? 0;

        $items = $reviews->map(function (ProductReview $review) {
            return [
                'id'         => $review->id,
                'user'       => $review->user?->name,
                'comment'    => $review->comment,
                'rating'     => $review->rating,
                'image_url'  => $review->image_url,
                'created_at' => $review->created_at->toDateString(),
            ];
        });

        return [
            'average_rating' => round($averageRating, 1),
            'total_reviews'  => $reviews->count(),
            'reviews'        => $items,
        ];
    }

    // =========================================================
    // هل اليوزر الحالي راجع هذا المنتج من قبل؟
    // =========================================================
    public function userAlreadyReviewed(int $productId, int $userId): bool
    {
        return ProductReview::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    // =========================================================
    // جلب كل المراجعات لمنتجات تاجر معين
    // =========================================================
    public function getReviewsByVendor(int $vendorId, ?int $productId = null): \Illuminate\Support\Collection
    {
        return ProductReview::query()
            ->whereHas('product', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->when($productId, function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->with(['user', 'product'])
            ->latest()
            ->get();
    }

    // =========================================================
    // تغيير التبديل الخاص بالظهور
    // =========================================================
    public function toggleVisibility(int $reviewId, int $vendorId): bool
    {
        $review = ProductReview::query()
            ->whereHas('product', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->findOrFail($reviewId);

        $review->is_visible = !$review->is_visible;
        return $review->save();
    }
}
