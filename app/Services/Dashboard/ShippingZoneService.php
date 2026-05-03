<?php



namespace App\Services\Dashboard;

use App\Models\ShippingZone;

class ShippingZoneService
{
    public function list()
    {
        return ShippingZone::latest()->paginate(10);
    }

    public function store(array $data): ShippingZone
    {
        $zone = new ShippingZone();
        $zone->setTranslations('name', [
            'ar' => (string)($data['name_ar'] ?? ''),
            'en' => (string)($data['name_en'] ?? ''),
        ]);
        $zone->price = (float)$data['price'];
        $zone->save();

        return $zone;
    }

    public function update(ShippingZone $zone, array $data): ShippingZone
    {
        $zone->setTranslations('name', [
            'ar' => (string)($data['name_ar'] ?? ''),
            'en' => (string)($data['name_en'] ?? ''),
        ]);
        $zone->price = (float)$data['price'];
        $zone->save();

        return $zone;
    }

    public function delete(ShippingZone $zone): bool
    {
        return $zone->delete();
    }
}
