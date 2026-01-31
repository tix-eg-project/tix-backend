<?php

namespace App\Http\Controllers\Web\Admin\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Country\CountryRequest;
use App\Models\Country;
use App\Services\Dashboard\CountryService;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        $countries = $this->countryService->index();
        return view('Admin.pages.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('Admin.pages.countries.create');
    }

    public function store(CountryRequest $request)
    {
        $this->countryService->store($request->validated());
        return redirect()->route('country.index')->with('success', 'Country Created Successfully.');
    }

    public function edit(string $id)
    {
        $country = Country::findOrFail($id);
        return view('Admin.pages.countries.edit', compact('country'));
    }

    public function update(CountryRequest $request, string $id)
    {
        $this->countryService->update($request->validated(), $id);
        return redirect()->route('country.index')->with('success', 'Country Updated Successfully.');;
    }

    public function destroy($id)
    {
        $this->countryService->destroy($id);
        return redirect()->route('country.index')->with('success', 'Country Deleted Successfully.');
    }
}
