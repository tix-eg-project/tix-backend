<?php

namespace App\Http\Controllers\Web\Admin\ShippingZone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ShippingSetting\ShippingZoneRequest;
use App\Models\ShippingZone;
use App\Services\Dashboard\ShippingZoneService;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    protected $zoneService;


    public function __construct(ShippingZoneService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $search = trim((string) $request->input('search', ''));

        $zones = ShippingZone::query()
            ->when($search !== '', function ($q) use ($search, $locale) {
                $term = "%{$search}%";
                $q->where(function ($qq) use ($term, $locale) {
                    $qq->where("name->$locale", 'like', $term)
                        ->orWhere("name->ar", 'like', $term)
                        ->orWhere("name->en", 'like', $term);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.pages.shipping_zones.index', compact('zones'));
    }


    public function create()
    {
        return view('Admin.pages.shipping_zones.create');
    }

    public function store(ShippingZoneRequest $request)
    {
        $this->zoneService->store($request->validated());
        return redirect()->route('admin.shipping_zones.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(ShippingZone $shippingZone)
    {
        return view('Admin.pages.shipping_zones.edit', compact('shippingZone'));
    }

    public function update(ShippingZoneRequest $request, ShippingZone $shippingZone)
    {
        $this->zoneService->update($shippingZone, $request->validated());
        return redirect()->route('admin.shipping_zones.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(ShippingZone $shippingZone)
    {
        $this->zoneService->delete($shippingZone);
        return redirect()->route('admin.shipping_zones.index')->with('success', __('messages.deleted_successfully'));
    }
}
