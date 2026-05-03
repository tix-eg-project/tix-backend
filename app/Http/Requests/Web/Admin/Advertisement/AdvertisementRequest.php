<?php
namespace App\Http\Requests\Web\Admin\Advertisement;

use Illuminate\Foundation\Http\FormRequest;

class AdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => __('messages.image_required'),
            'image.image' => __('messages.image_invalid'),
            'image.mimes' => __('messages.image_mimes'),
            'image.max' => __('messages.image_max'),
        ];
    }
}
