<?php

namespace App\Http\Controllers\Web\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Variant\ProductItemVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Services\Dashboard\Variant\ProductVariantItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductItemVariantController extends Controller
{
    public function __construct(private ProductVariantItemService $service) {}

    public function index(Product $product): View
    {
        $items = $product->variantItems()->latest('id')->get();

        return view('Admin.pages.products.product_variant_items.index', compact('product', 'items'));
    }


    public function create(Product $product): View
    {

        $variants = ProductVariant::with(['values' => fn($q) => $q->orderBy('id')])
            ->orderBy('id')
            ->get();

        return view('Admin.pages.products.product_variant_items.create', compact('product', 'variants'));
    }


    public function store(ProductItemVariantRequest $request, Product $product): RedirectResponse
    {
        $payload = $this->buildPayloadFromRequest($request);

        // إضافة product_id إلى الـ payload
        $payload['product_id'] = $product->id; 

        $this->service->create($product, $payload);

        return redirect()
            ->route('products-variant.index', $product)
            ->with('success', __('messages.created_successfully'));
    }


    public function show(Product $product, ProductVariantItem $variantItem): View
    {
        $this->assertBelongsToProduct($product, $variantItem);

        return view('Admin.pages.products.product_variant_items.show', [
            'product' => $product,
            'item'    => $variantItem,
        ]);
    }


    public function edit(Product $product, ProductVariantItem $variantItem): View
    {
        $this->assertBelongsToProduct($product, $variantItem);


        $variants = ProductVariant::with(['values' => fn($q) => $q->orderBy('id')])
            ->orderBy('id')
            ->get();


        $item = $variantItem;

        return view(
            'Admin.pages.products.product_variant_items.edit',
            compact('product', 'variants', 'item')
        );
    }


    public function update(ProductItemVariantRequest $request, Product $product, ProductVariantItem $variantItem): RedirectResponse
    {
        $this->assertBelongsToProduct($product, $variantItem);

        $payload = $this->buildPayloadFromRequest($request, updating: true);
        $payload['product_id'] = $product->id;

        // حدِّث
        $this->service->update($variantItem, $payload);

        return redirect()
            ->route('products-variant.index', $product)
            ->with('success', __('messages.updated_successfully'));
    }


    public function destroy(Product $product, ProductVariantItem $variantItem): RedirectResponse
    {
        $this->assertBelongsToProduct($product, $variantItem);

        $this->service->delete($variantItem);

        return redirect()
            ->route('products-variant.index', $product)
            ->with('success', __('messages.deleted_successfully'));
    }


    private function buildPayloadFromRequest(ProductItemVariantRequest $request, bool $updating = false): array
    {
        $variantValues = (array) $request->input('variant_values', []);

        // موحّد: نحفظ بالشكل المطلوب
        $selections = collect($variantValues)
            ->filter(fn($valueId) => filled($valueId))
            ->map(fn($valueId, $variantId) => [
                'product_variant_id'       => (int) $variantId,
                'product_variant_value_id' => (int) $valueId,
            ])
            ->values()
            ->all();

        $payload = [];


        if (!$updating || $request->has('variant_values')) {
            $payload['selections'] = $selections;
        }

        if ($request->filled('price')) {
            $payload['price'] = $request->float('price');
        }
        if ($request->has('quantity')) {
            $payload['quantity'] = $request->input('quantity');
        }
        if ($request->has('is_active')) {
            $payload['is_active'] = $request->boolean('is_active');
        }

        return $payload;
    }


    private function assertBelongsToProduct(Product $product, ProductVariantItem $variantItem): void
    {
        if ((int) $variantItem->product_id !== (int) $product->id) {
            abort(404);
        }
    }
}
