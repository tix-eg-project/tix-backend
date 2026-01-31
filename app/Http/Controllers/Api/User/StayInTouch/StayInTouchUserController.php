<?php

namespace App\Http\Controllers\Api\User\StayInTouch;

use App\Http\Controllers\Controller;
use App\Models\StayInTouch;
use App\Helpers\ApiResponseHelper;

class StayInTouchUserController extends Controller
{
    public function index()
    {
        $row = StayInTouch::first();


        if (! $row) {
            return ApiResponseHelper::success(__('messages.retrieved_successfully'), []);
        }

        $locale = app()->getLocale();


        $address    = $row->address[$locale]    ?? $row->address    ?? [];
        $workHours  = $row->work_hours[$locale] ?? $row->work_hours ?? '';
        $webLinks   = $row->web_link[$locale]   ?? $row->web_link   ?? [];

        $item = [
            'address'        => array_values((array) $address),
            'phones'         => array_values((array) ($row->phones ?? [])),
            'work_hours'     => (string) $workHours,
            'map_link'       => (string) ($row->map_embed_url ?? $row->map_link ?? ''),
            'whatsapp_link'  => (string) ($row->whatsapp_link ?? ''),
            'web_link'       => array_values((array) $webLinks),
            'email'          => (string) ($row->email ?? ''),
        ];

        return ApiResponseHelper::success(__('messages.retrieved_successfully'), [$item]);
    }
}
