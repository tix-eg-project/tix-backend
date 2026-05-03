<?php

namespace App\Services\Dashboard;

use App\Models\City;

class CityService
{
    public function index()
    {
        $search = request('search');
        $query = City::with('country');
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
        City::query()->create($data);
    }

    public function update(array $data, string $id)
    {
        City::query()->findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        City::query()->findOrFail($id)->delete();
    }
}
