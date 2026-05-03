<?php

namespace App\Http\Controllers\Web\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Variant\VariantRequest;
use App\Models\ProductVariant;
use App\Services\Dashboard\Variant\VariantService; // نفس ستايل CountryService
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function __construct(private VariantService $service) {}


    public function index(): View
    {
        $variants = $this->service->index();
        return view('Admin.pages.variants.index', compact('variants'));
    }

    public function create(): View
    {
        return view('Admin.pages.variants.create');
    }

    public function store(VariantRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());
        return redirect()->route('variants.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(ProductVariant $variant): View
    {
        return view('Admin.pages.variants.edit', compact('variant'));
    }

    public function update(VariantRequest $request, ProductVariant $variant): RedirectResponse
    {
        $this->service->update($request->validated(), (string)$variant->id);
        return redirect()->route('variants.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $this->service->destroy($variant->id);
        return redirect()->route('variants.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
