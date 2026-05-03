<?php

namespace App\Http\Controllers\Web\Admin\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\City\CityRequest;
use App\Models\City;
use App\Models\Country;
use App\Services\Dashboard\CityService;

class CityController extends Controller
{

    protected $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index()
    {
        $cities = $this->cityService->index();
        return view('Admin.pages.cities.index', compact('cities'));
    }

    public function create()
    {
        $coutries = Country::all();
        return view('Admin.pages.cities.create', compact('coutries'));
    }

    public function store(CityRequest $request)
    {
        $this->cityService->store($request->validated());
        return redirect()->route('cities.index')->with('success', 'City Created Successfully.');;
    }

    public function edit(string $id)
    {
        $city = City::query()->findOrFail($id);
        $countries = Country::all();
        return view('Admin.pages.cities.edit', compact('city', 'countries'));
    }

    public function update(CityRequest $request, string $id)
    {
        $this->cityService->update($request->validated(), $id);
        return redirect()->route('cities.index')->with('success', 'City Updated Successfully.');
    }

    public function destroy(string $id)
    {
        $this->cityService->destroy($id);
        return redirect()->route('cities.index')->with('success', 'City Deleted Successfully.');
    }
}
