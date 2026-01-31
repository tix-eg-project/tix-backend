<?php

namespace App\Http\Controllers\Web\Vendor\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Product\StoreProductRequest;
use App\Http\Requests\Web\Admin\Product\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\Dashboard\ProductService;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    private function currentVendorId(): ?int
    {
        return auth('vendor')->id() ?? auth()->id();
    }

    private function assertVendorOwns(Product $product): void
    {
        $vendorId = $this->currentVendorId();
        if ((int) $product->vendor_id !== (int) $vendorId) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $locale    = app()->getLocale();
        $search    = trim((string) $request->input('search', ''));
        $vendorId  = $this->currentVendorId();

        $catId     = $request->integer('category_id');
        $subId     = $request->integer('subcategory_id');
        $brandId   = $request->integer('brand_id');
        $statusStr = $request->input('status');


        $categories = Category::select('id', 'name')->orderBy('id')->get();
        $subcategories = SubCategory::select('id', 'name')
            ->when($catId, fn($q) => $q->where('category_id', $catId))
            ->orderBy('id')->get();
        $brands = Brand::select('id', 'name')->orderBy('id')->get();

        $products = Product::query()
            ->where('vendor_id', $vendorId)
            ->with(['brand:id,name', 'category:id,name', 'subcategory:id,name'])
            ->when($search !== '', function ($q) use ($search, $locale) {
                $q->where(function ($qb) use ($search, $locale) {
                    $qb->where("name->$locale", 'like', "%{$search}%")
                        ->orWhere("short_description->$locale", 'like', "%{$search}%");
                });
            })
            ->when($catId,  fn($q) => $q->where('category_id', $catId))
            ->when($subId,  fn($q) => $q->where('subcategory_id', $subId))
            ->when($brandId, fn($q) => $q->where('brand_id', $brandId))
            ->when(in_array($statusStr, ['1', '2'], true), fn($q) => $q->where('status', (int)$statusStr))
            ->withCount('variantItems')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('Vendor.pages.products.index', compact(
            'products',
            'categories',
            'subcategories',
            'brands'
        ));
    }


    public function create()
    {
        $locale = app()->getLocale();
        $categories    = Category::select('id', 'name')->orderBy("name->$locale")->get();
        $subcategories = Subcategory::select('id', 'name')->orderBy("name->$locale")->get();
        $brands        = Brand::select('id', 'name')->orderBy("name->$locale")->get();

        return view('Vendor.pages.products.create', compact('categories', 'subcategories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        // الـ Service هيحط vendor_id تلقائيًا لو المستخدم Vendor
        $this->productService->store($request->validated());

        return redirect()
            ->route('vendor.products.index')
            ->with('success', __('messages.product_added'));
    }

    public function edit(Product $product)
    {
        $this->assertVendorOwns($product);

        $locale = app()->getLocale();
        $categories    = Category::select('id', 'name')->orderBy("name->$locale")->get();
        $subcategories = Subcategory::select('id', 'name')->orderBy("name->$locale")->get();
        $brands        = Brand::select('id', 'name')->orderBy("name->$locale")->get();

        return view('Vendor.pages.products.update', compact('product', 'categories', 'subcategories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->assertVendorOwns($product);

        $this->productService->update($product, $request->validated());

        return redirect()
            ->route('vendor.products.index')
            ->with('success', __('messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        $this->assertVendorOwns($product);

        $this->productService->delete($product);

        return redirect()
            ->route('vendor.products.index')
            ->with('success', __('messages.product_deleted'));
    }
}
