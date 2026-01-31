<?php

namespace App\Http\Controllers\Web\Vendor\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Variant\VariantRequest;
use App\Models\ProductVariant;
use App\Services\Dashboard\Variant\VariantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorProductVariantController extends Controller
{
    public function __construct(private VariantService $service) {}


    public function index(): View
    {
        $variants = $this->service->index();
        return view('Vendor.pages.variants.index', compact('variants'));
    }

    public function create(): View
    {
        return view('Vendor.pages.variants.create');
    }

    public function store(VariantRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());
        return redirect()->route('vendor.variants.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(ProductVariant $variant): View
    {
        return view('Vendor.pages.variants.edit', compact('variant'));
    }

    public function update(VariantRequest $request, ProductVariant $variant): RedirectResponse
    {
        $this->service->update($request->validated(), (string)$variant->id);
        return redirect()->route('vendor.variants.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $this->service->destroy($variant->id);
        return redirect()->route('vendor.variants.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
