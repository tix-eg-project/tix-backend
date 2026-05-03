<?php

namespace App\Http\Controllers\Web\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Web\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Dashboard\CategoryService;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $query = Category::latest();
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->paginate(10);
        return view('Admin.pages.categories.index', compact('categories'));
    }


    public function create()
    {
        return view('Admin.pages.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {

        $this->categoryService->store($request->validated());
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');;
    }

    public function edit(Category $category)
    {

        return view('Admin.pages.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');;
    }



    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');;
    }
}
