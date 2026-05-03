<?php

namespace App\Http\Controllers\Web\Admin\Brand;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\Dashboard\BrandService;
use Illuminate\Http\Request;
use App\Http\Requests\Web\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Web\Admin\Brand\UpdateBrandRequest;

class AdminBrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(BrandService $brandService)
    {
        $brands = $brandService->index();
        return view('Admin.pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('Admin.pages.brands.create');
    }
    public function store(StoreBrandRequest $request)
    {
        $this->brandService->store($request->validated());
        return redirect()->route('brands.index')->with('success', 'Brand Created Successfully.');
    }
    public function edit(Brand $brand)
    {
        return view('Admin.pages.brands.edit', compact('brand'));
    }
    public function update(UpdateBrandRequest $request, string $id)
    {
        $this->brandService->update($request->validated(), $id);
        return redirect()->route('brands.index')->with('success', 'Brand Updated Successfully.');
    }
    public function destroy($id)
    {
        $this->brandService->destroy($id);
        return redirect()->route('brands.index');
    }
}
