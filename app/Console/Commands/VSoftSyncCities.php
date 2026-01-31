<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Shipping\VSoftShippingService;
use App\Models\VSoftCity;
use App\Models\ShippingZone;

class VSoftSyncCities extends Command
{
    protected $signature = 'vsoft:sync-cities';
    protected $description = 'Sync VSoft cities and (optionally) auto-map to local zones';

    public function handle(VSoftShippingService $api)
    {
        $rows = $api->getCities();
        if (!is_array($rows) || empty($rows)) {
            $this->error('No cities returned from VSoft.');
            return self::FAILURE;
        }

        $autoMapNames = [1 => 'Zone 1', 2 => 'Zone 2', 3 => 'Zone 3', 4 => 'Zone 4'];
        $nameToId = ShippingZone::all()
            ->mapWithKeys(fn($z) => [trim(is_array($z->name) ? ($z->name['en'] ?? $z->name['ar'] ?? '') : $z->name) => $z->id])
            ->toArray();

        $count = 0;
        foreach ($rows as $r) {
            $mappedZoneId = null;
            $vz = $r['zoneID'] ?? null;
            if ($vz && isset($autoMapNames[$vz]) && isset($nameToId[$autoMapNames[$vz]])) {
                $mappedZoneId = $nameToId[$autoMapNames[$vz]];
            }

            VSoftCity::updateOrCreate(
                ['vsoft_city_id' => $r['cityID']],
                [
                    'name'             => trim((string) ($r['cityName'] ?? '')),
                    'vsoft_zone_id'    => $vz,
                    'shipping_zone_id' => $mappedZoneId,
                ]
            );
            $count++;
        }

        $this->info("Synced {$count} cities.");
        return self::SUCCESS;
    }
}
