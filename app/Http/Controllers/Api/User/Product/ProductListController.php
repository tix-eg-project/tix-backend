<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\Dashboard\Product\ProductListService;
use Illuminate\Http\JsonResponse;

class ProductListController extends Controller
{
    public function __construct(private ProductListService $service) {}

    public function index(): JsonResponse
    {
        $perPage = (int) request('per_page', 15);

        $paginator = $this->service
            ->list($perPage)
            ->through(fn($p) => (new ProductResource($p))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }

    public function productsByCategory($category_id): JsonResponse
    {
        $perPage = (int) request('per_page', 15);

        $paginator = $this->service
            ->byCategory((int) $category_id, $perPage)
            ->through(fn($p) => (new ProductResource($p))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }

    public function productsBySubcategory($subcategory_id): JsonResponse
    {
        $perPage = (int) request('per_page', 15);

        $paginator = $this->service
            ->bySubcategory((int) $subcategory_id, $perPage)
            ->through(fn($p) => (new ProductResource($p))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }

    public function productsDiscount($discount): JsonResponse
    {
        $perPage = (int) request('per_page', 10);

        $paginator = $this->service
            ->byExactDiscount((float) $discount, $perPage)
            ->through(fn($p) => (new ProductResource($p))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }
}
