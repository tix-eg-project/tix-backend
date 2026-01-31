<?php

namespace App\Services\Dashboard;

use App\Models\Country;

class CountryService
{

    public function index()
    {
        $search = request('search');
        $query = Country::query();
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
        Country::query()->create($data);
    }

    public function update(array $data, string $id)
    {
        Country::query()->findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        Country::query()->findOrFail($id)->delete();
    }
}
