<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\Dashboard\Product\SearchService;
use Illuminate\Http\Request;



class SearchController extends Controller
{
    public function __construct(private SearchService $service) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q'        => ['nullable', 'string', 'max:255'],
            'page'     => ['nullable', 'integer', 'gte:1'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $perPage = (int)($validated['per_page'] ?? 16);

        $paginator = $this->service
            ->searchProducts(
                query: $validated['q'] ?? null,
                perPage: $perPage,
            )
            ->through(fn($p) => (new ProductResource($p))->toArray($request));

        return ApiResponseHelper::paginated($paginator, 'messages.product_list_retrieved');
    }
}
