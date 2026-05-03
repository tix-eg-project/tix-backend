<?php

namespace App\Http\Requests\Api\Favorites;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'المنتج',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
