<?php

namespace App\Services\Dashboard\Variant;

use App\Models\ProductVariant;

class VariantService
{
    public function index()
    {
        $search = request('search');
        $query  = ProductVariant::query();

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

        ProductVariant::query()->create($data);
    }

    public function update(array $data, string $id)
    {
        ProductVariant::query()->findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        ProductVariant::query()->findOrFail($id)->delete();
    }
}
