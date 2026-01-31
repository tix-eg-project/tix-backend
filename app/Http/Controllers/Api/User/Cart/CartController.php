<?php

namespace App\Http\Controllers\Api\User\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\AddToCartRequest;
use App\Http\Requests\Api\Cart\UpdateCartItemRequest;
use App\Helpers\ApiResponseHelper;
use App\Services\Cart\CartService;
use DomainException;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}

    public function store(AddToCartRequest $request)
    {
        try {
            $variantId = $request->input('product_variant_item_id', $request->input('product_variant_id'));

            $res = $this->service->add(
                productId: (int)$request->product_id,
                quantity: (int)($request->quantity ?? 1),
                productVariantItemId: $variantId ? (int)$variantId : null
            );

            if (isset($res['status']) && $res['status'] === false) {
                return ApiResponseHelper::error($res['message'] ?? __('messages.cart.item_exists'), 422);
            }

            return ApiResponseHelper::success('messages.cart.item_added');

        } catch (DomainException $e) {
            return ApiResponseHelper::error($e->getMessage(), 422);
        }
    }


    public function index()
    {
        $items = $this->service->all();

        if (count($items) === 0) {
            return ApiResponseHelper::success('messages.cart.empty', []);
        }

        return ApiResponseHelper::success('messages.cart.retrieved', $items);
    }

    public function update(UpdateCartItemRequest $request, int $id)
    {
        try {
            $itemData = $this->service->update($id, $request->quantity);
            return ApiResponseHelper::success('messages.cart.updated', $itemData);
        } catch (DomainException $e) {
            return ApiResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function destroy(int $id)
    {
        $this->service->remove($id);
        return ApiResponseHelper::success('messages.cart.removed');
    }

    public function clear()
    {
        $this->service->clear();
        return ApiResponseHelper::success('messages.cart.cleared');
    }
}
