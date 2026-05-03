<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VSoftCity;
use App\Models\ShippingZone;
use Illuminate\Validation\Rule;

class AdminVSoftCityController extends Controller
{
    public function index(Request $request)
    {
        $q              = trim((string)$request->query('q', ''));
        $mapping        = $request->query('mapping');
        $vsoftZoneId    = $request->query('vsoft_zone_id');
        $shippingZoneId = $request->query('shipping_zone_id');
        $perPage        = (int)($request->query('per_page', 50)) ?: 50;

        $cities = VSoftCity::with(['shippingZone:id,name'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('vsoft_city_id', 'like', "%{$q}%")
                        ->orWhere('id', 'like', "%{$q}%");
                });
            })
            ->when($mapping === 'mapped', fn($qry) => $qry->whereNotNull('shipping_zone_id'))
            ->when($mapping === 'unmapped', fn($qry) => $qry->whereNull('shipping_zone_id'))
            ->when($vsoftZoneId !== null && $vsoftZoneId !== '', fn($qry) => $qry->where('vsoft_zone_id', $vsoftZoneId))
            ->when($shippingZoneId !== null && $shippingZoneId !== '', fn($qry) => $qry->where('shipping_zone_id', $shippingZoneId))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $zones = ShippingZone::orderBy('id')->get(['id', 'name']);

        $vsoftZones = VSoftCity::query()
            ->select('vsoft_zone_id')
            ->whereNotNull('vsoft_zone_id')
            ->distinct()
            ->orderBy('vsoft_zone_id')
            ->pluck('vsoft_zone_id');

        return view('Admin.pages.vsoft_cities.index', compact('cities', 'q', 'zones', 'vsoftZones'));
    }

    public function edit(int $id)
    {
        $city  = VSoftCity::findOrFail($id);
        $zones = ShippingZone::orderBy('id')->get(['id', 'name']);
        return view('Admin.pages.vsoft_cities.edit', compact('city', 'zones'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'shipping_zone_id' => ['nullable', Rule::exists('shipping_zones', 'id')],
        ]);

        $city = VSoftCity::findOrFail($id);
        $city->update(['shipping_zone_id' => $data['shipping_zone_id'] ?? null]);

        return redirect()
            ->route('admin.vsoft-cities.index', $request->only(['q', 'mapping', 'vsoft_zone_id', 'shipping_zone_id', 'per_page', 'page']))
            ->with('ok', __('messages.saved_successfully'));
    }

    public function bulkMap(Request $request)
    {
        $data = $request->validate([
            'city_ids'         => ['required', 'array', 'min:1'],
            'city_ids.*'       => ['integer', 'exists:vsoft_cities,id'],
            'shipping_zone_id' => ['nullable', 'integer', 'exists:shipping_zones,id'],
        ]);

        VSoftCity::whereIn('id', $data['city_ids'])
            ->update(['shipping_zone_id' => $data['shipping_zone_id'] ?? null]);

        return back()->with('ok', __('messages.bulk_updated_successfully'));
    }
}
