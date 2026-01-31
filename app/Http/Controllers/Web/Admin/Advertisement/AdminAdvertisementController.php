<?php

namespace App\Http\Controllers\Web\Admin\Advertisement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Advertisement\AdvertisementRequest;
use App\Services\Dashboard\AdvertisementService;
use App\Models\Advertisement;

class AdminAdvertisementController extends Controller
{
    protected $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
        $this->advertisementService = $advertisementService;
    }

    public function index()
    {
        $advertisements = Advertisement::all();
        return view('Admin.pages.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        return view('Admin.pages.advertisements.create');
    }

    public function store(AdvertisementRequest $request)
    {
        $this->advertisementService->store($request->validated());
        return redirect()->route('advertisements.index')->with('success', __('messages.advertisement_added'));
    }

    public function destroy(Advertisement $advertisement)
    {
        $this->advertisementService->delete($advertisement);
        return redirect()->route('advertisements.index')->with('success', __('messages.advertisement_deleted'));
    }
}
