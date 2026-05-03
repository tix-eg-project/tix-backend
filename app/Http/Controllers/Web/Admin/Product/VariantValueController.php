<?php

namespace App\Http\Controllers\Web\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Variant\VariantValueRequest;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VariantValueController extends Controller
{

    public function index(ProductVariant $variant, Request $request): View
    {
        $query = ProductVariantValue::query()
            //    ->where('product_variants_id', $variant->id)
            ->latest('id');

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%");
            });
        }

        $values   = $query->paginate(10)->appends(['search' => $search]);
        $variants = ProductVariant::orderByDesc('id')->get();

        return view('Admin.pages.variants.values.index', compact('variant', 'values', 'variants', 'search'));
    }



    public function create(ProductVariant $variant): View
    {
        $variants = ProductVariant::query()->orderByDesc('id')->get();
        return view('Admin.pages.variants.values.create', compact('variant', 'variants'));
    }

    public function store(VariantValueRequest $request): RedirectResponse
    {
        $data      = $request->validated();
        $variantId = (int) $data['variant_id'];

        DB::transaction(function () use ($data, $variantId) {
            $this->createValueRow($variantId, $data['name'] ?? [], $data['meta'] ?? null);
        });

        return redirect()
            ->route('variant-values.index', $variantId)
            ->with('success', __('messages.created_successfully'));
    }


    public function destroy(ProductVariantValue $value): RedirectResponse
    {
        $value->delete();
        return redirect()
            ->route('variant-values.index', $value->variant_id)
            ->with('success', __('messages.deleted_successfully'));
    }
    private function createValueRow(int $variantId, array $name, $meta): void
    {
        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            $meta = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        ProductVariantValue::query()->create([
            'product_variants_id' => $variantId,
            'name' => $name,
            'meta' => $meta,
        ]);
    }
}
