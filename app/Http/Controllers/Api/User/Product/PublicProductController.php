<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\Dashboard\Product\ProductUserService;
use Illuminate\Http\JsonResponse;

class PublicProductController extends Controller
{
    public function __construct(private ProductUserService $service) {}

    public function discountedProducts(): JsonResponse
    {
        $perPage = (int) request('per_page', 10);
        $min     = (float) request('min', 0);

        $paginator = $this->service
            ->getDiscounted($perPage, $min)
            ->through(fn($p) => (new ProductResource($p))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }

    public function productsByOffer($offerId): JsonResponse
    {
        $perPage = (int) request('per_page', 10);

        $paginator = $this->service
            ->getByOffer((int) $offerId, $perPage)
            ->through(fn($p) => (new ProductResource($p->loadMissing('offers')))->toArray(request()));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }
}
