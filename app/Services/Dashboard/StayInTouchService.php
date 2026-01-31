<?php

namespace App\Services\Dashboard;

use App\Models\StayInTouch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StayInTouchService
{
    public function get()
    {
        return StayInTouch::first();
    }

    public function storeOrUpdate(array $data)
    {
        return DB::transaction(function () use ($data) {

            // لفّ آمن للقيم (حتى لو null أو string واحدة)
            $addressAr = array_values(array_filter(Arr::wrap($data['address_ar'] ?? [])));
            $addressEn = array_values(array_filter(Arr::wrap($data['address_en'] ?? [])));
            $phones    = array_values(array_filter(Arr::wrap($data['phones'] ?? [])));
            $webLinks  = array_values(array_filter(Arr::wrap($data['web_link'] ?? [])));

            $payload = [
                // أعمدة الجدول الفعلية (JSON)
                'address' => [
                    'ar' => $addressAr,                          // أمثلة: ["عنوان 1","عنوان 2"]
                    'en' => $addressEn,
                ],
                'phones' => $phones,                            // JSON list

                'work_hours' => [
                    'ar' => (string)($data['work_hours_ar'] ?? ''),
                    'en' => (string)($data['work_hours_en'] ?? ''),
                ],

                'map_link'      => $this->convertMapLinkToEmbed($data['map_link'] ?? ''),
                'whatsapp_link' => (string)($data['whatsapp_link'] ?? ''),

                'web_link' => [
                    // لو عندك ويب عربي ضيفه هنا بدل مصفوفة فاضية
                    'ar' => [],
                    'en' => $webLinks,
                ],

                // عمود email عندك JSON، هنخزن سترينج بسيط (هيتخزن JSON string)
                'email' => (string)($data['email'] ?? ''),
            ];

            $entry = StayInTouch::query()->first();

            return $entry
                ? tap($entry)->update($payload)
                : StayInTouch::query()->create($payload);
        });
    }

    /**
     * Convert map link to embeddable URL format if needed
     */
    private function convertMapLinkToEmbed(?string $mapLink): string
    {
        $mapLink = (string)($mapLink ?? '');

        if ($mapLink === '') {
            return '';
        }

        if (str_contains($mapLink, 'output=embed') || str_contains($mapLink, '/embed?')) {
            return $mapLink;
        }

        if (preg_match('/[?&]q=([\d\.\-]+),([\d\.\-]+)/', $mapLink, $m)) {
            [$all, $lat, $lng] = $m;
            return "https://www.google.com/maps?q={$lat},{$lng}&output=embed";
        }

        return $mapLink;
    }
}
