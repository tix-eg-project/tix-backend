<?php

namespace App\Services\Dashboard;


use App\Models\Brand;

class BrandService
{

    public function index()
    {
        $search = request('search');
        $query = Brand::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name->ar', 'like', '%' . $search . '%')
                    ->orWhere('name->en', 'like', '%' . $search . '%');
            });
        }
        return $query->latest()->paginate(10);
    }


    public function store(array $data)
    {
        Brand::query()->create($data);
    }

    public function update(array $data, string $id)
    {
        Brand::query()->findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        Brand::query()->findOrFail($id)->delete();
    }
}
