<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailsResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductDetailsController extends Controller
{
    public function show($id): JsonResponse
    {
        $product = Product::query()
            ->with([
                'brand',
                'subcategory.category',
                'variants.values',
                'variantItems',
                'faqs',
                'reviews' => fn ($q) => $q->with('user:id,name')->latest()->limit(30),
            ])
            ->find($id);




        if (!$product) {
            return ApiResponseHelper::error(__('messages.products.not_found'), 404, null);
        }

        return ApiResponseHelper::success(
            'messages.products.show_success',
            ProductDetailsResource::make($product)
        );
    }
}
