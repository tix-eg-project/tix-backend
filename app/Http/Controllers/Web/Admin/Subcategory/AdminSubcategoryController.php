<?php

namespace App\Http\Controllers\Web\Admin\Subcategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Web\Admin\Subcategory\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\Dashboard\SubCategoryService;
use Illuminate\Http\Request;

class AdminSubcategoryController extends Controller
{
    protected $subCategoryService;

    public function __construct(SubCategoryService $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
    }



    public function index(Request $request)
    {
        $loc = app()->getLocale();
        $search = trim((string) $request->get('search'));

        $subcategories = Subcategory::query()
            ->with('category:id,name')
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', (int) $request->category_id);
            })
            ->when($search !== '', function ($q) use ($search, $loc) {
                $q->where(function ($qq) use ($search, $loc) {
                    $qq->where("name->{$loc}", 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere("description->{$loc}", 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query()); 

        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('Admin.pages.subcategories.index', compact('subcategories', 'categories'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('Admin.pages.subcategories.create', compact('categories'));
    }

    public function store(StoreSubcategoryRequest $request)
    {
        $this->subCategoryService->store($request->validated());
        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return redirect()->route('subcategories.index')->with('error', 'Subcategory not found.');
        }

        $categories = Category::all();
        return view('Admin.pages.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory)
    {
        $this->subCategoryService->update($subcategory, data: $request->validated());
        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $this->subCategoryService->delete($subcategory);
        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
