<?php

namespace App\Http\Requests\Web\Admin\StayInTouch;

use Illuminate\Foundation\Http\FormRequest;

class StayInTouchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'address_ar'     => 'required|array|min:1',
            'address_ar.*'   => 'required|string',

            'address_en'     => 'required|array|min:1',
            'address_en.*'   => 'required|string',

            'phones'         => 'required|array|min:1',
            'phones.*'       => 'required|string',

            'work_hours_ar'  => 'required|string',
            'work_hours_en'  => 'required|string',

            'map_link'       => 'required|url',
            'whatsapp_link'  => 'nullable|url',

            'web_link'       => 'nullable|array',
            'web_link.*'     => 'nullable|url',

            'email'          => 'required|email',
        ];
    }

    public function attributes(): array
    {
        return [
            'address_ar'     => 'Address (Arabic)',
            'address_ar.*'   => 'Address (Arabic)',

            'address_en'     => 'Address (English)',
            'address_en.*'   => 'Address (English)',

            'phones'         => 'Phone numbers',
            'phones.*'       => 'Phone number',

            'web_link'       => 'Website link',
            'web_link.*'     => 'Website link',

            'work_hours_ar'  => 'Working hours (Arabic)',
            'work_hours_en'  => 'Working hours (English)',
            'map_link'       => 'Location link',
            'whatsapp_link'  => 'WhatsApp link',
            'email'          => 'Email',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
