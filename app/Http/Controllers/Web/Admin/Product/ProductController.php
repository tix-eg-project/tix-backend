<?php

namespace App\Http\Controllers\Web\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Product\StoreProductRequest;
use App\Http\Requests\Web\Admin\Product\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
use App\Services\Dashboard\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $search = trim((string) $request->input('search', ''));

        $products = Product::query()
            ->with([
                'brand:id,name',
                'category:id,name',
                'subcategory:id,name',
                'vendor:id,name',
            ])
            // فلتر بالقسم الرئيسي
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', (int) $request->category_id);
            })
            // فلتر بالقسم الفرعي
            ->when($request->filled('subcategory_id'), function ($q) use ($request) {
                $q->where('subcategory_id', (int) $request->subcategory_id);
            })
            // فلتر بالماركة
            ->when($request->filled('brand_id'), function ($q) use ($request) {
                $q->where('brand_id', (int) $request->brand_id);
            })
            // فلتر بالحالة
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            // (اختياري) فلتر بالمورّد
            ->when($request->filled('vendor_id'), function ($q) use ($request) {
                $q->where('vendor_id', (int) $request->vendor_id);
            })
            // البحث (يدعم JSON أو نص عادي)
            ->when($search !== '', function ($q) use ($search, $locale) {
                $q->where(function ($qb) use ($search, $locale) {
                    $like = "%{$search}%";
                    $qb->where("name->$locale", 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere("short_description->$locale", 'like', $like)
                        ->orWhere('short_description', 'like', $like);
                    // ->orWhere('sku', 'like', $like);
                });
            })
            ->withCount('variantItems')
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query()); // يحافظ على الفلاتر/البحث في الباجينج

        // بيانات القوايم للفلاتر
        $categories    = Category::select('id', 'name')->orderBy('name')->get();
        // لو تحب تظهر أقسام فرعية خاصة بالقسم المختار فقط:
        $subcategories = Subcategory::select('id', 'name')
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', (int)$request->category_id))
            ->orderBy('name')->get();
        $brands        = Brand::select('id', 'name')->orderBy('name')->get();
        // (اختياري) للمورّد
        $vendors       = class_exists(Vendor::class)
            ? Vendor::select('id', 'name')->orderBy('name')->get()
            : collect();

        return view('Admin.pages.products.index', compact(
            'products',
            'categories',
            'subcategories',
            'brands',
            'vendors'
        ));
    }


    public function create()
    {
        $locale = app()->getLocale();
        $categories    = Category::select('id', 'name')->orderBy("name->$locale")->get();
        $subcategories = Subcategory::select('id', 'name')->orderBy("name->$locale")->get();
        $brands        = Brand::select('id', 'name')->orderBy("name->$locale")->get();

        return view('Admin.pages.products.create', compact('categories', 'subcategories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->productService->store($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product_added'));
    }

    public function edit(Product $product)
    {
        $product->load(['faqs', 'reviews.user', 'comments.user']);
        $locale = app()->getLocale();
        $categories    = Category::select('id', 'name')->orderBy("name->$locale")->get();
        $subcategories = Subcategory::select('id', 'name')->orderBy("name->$locale")->get();
        $brands        = Brand::select('id', 'name')->orderBy("name->$locale")->get();

        return view('Admin.pages.products.update', compact('product', 'categories', 'subcategories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->update($product, $request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product_deleted'));
    }
}
