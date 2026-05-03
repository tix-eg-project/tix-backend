<?php

namespace App\Http\Requests\Web\Auth\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:vendors,email',
            'phone'         => 'required|string|max:255',
            'password'      => 'required|string|min:8|confirmed',

            'company_name'  => 'required|string|max:255',
            'description'   => 'required|string|max:255',

            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',

            'address'       => 'required|string|max:255',
            'postal_code'   => 'required|string|max:255',

            'vodafone_cash' => 'required|string|max:255',
            'instapay'      => 'required|string|max:255',

            'type_business' => 'required|string|max:255',

            'category_id'   => 'required|integer|exists:categories,id',
            'country_id'    => 'required|integer|exists:countries,id',
            'city_id'       => 'required|integer|exists:cities,id',

            'id_card_front_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'id_card_back_image'  => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'The name field is required.',
            'name.string'          => 'The name must be a string.',
            'name.max'             => 'The name may not be greater than 255 characters.',

            'email.required'       => 'The email field is required.',
            'email.string'         => 'The email must be a string.',
            'email.email'          => 'The email must be a valid email address.',
            'email.max'            => 'The email may not be greater than 255 characters.',
            'email.unique'         => 'The email has already been taken.',

            'phone.required'       => 'The phone field is required.',
            'phone.string'         => 'The phone must be a string.',
            'phone.max'            => 'The phone may not be greater than 255 characters.',

            'password.required'    => 'The password field is required.',
            'password.string'      => 'The password must be a string.',
            'password.min'         => 'The password must be at least 8 characters.',
            'password.confirmed'   => 'The password confirmation does not match.',

            'company_name.required' => 'The company name field is required.',
            'company_name.string'  => 'The company name must be a string.',
            'company_name.max'     => 'The company name may not be greater than 255 characters.',

            'description.required' => 'The description field is required.',
            'description.string'   => 'The description must be a string.',
            'description.max'      => 'The description may not be greater than 255 characters.',

            'image.image'          => 'The image must be an image.',
            'image.mimes'          => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max'            => 'The image may not be greater than 4096 kilobytes.',

            'address.required'     => 'The address field is required.',
            'address.string'       => 'The address must be a string.',
            'address.max'          => 'The address may not be greater than 255 characters.',

            'postal_code.required' => 'The postal code field is required.',
            'postal_code.string'   => 'The postal code must be a string.',
            'postal_code.max'      => 'The postal code may not be greater than 255 characters.',

            'vodafone_cash.required' => 'The vodafone cash field is required.',
            'vodafone_cash.string'   => 'The vodafone cash must be a string.',
            'vodafone_cash.max'      => 'The vodafone cash may not be greater than 255 characters.',

            'instapay.required'    => 'The instapay field is required.',
            'instapay.string'      => 'The instapay must be a string.',
            'instapay.max'         => 'The instapay may not be greater than 255 characters.',

            'type_business.required' => 'The type business field is required.',
            'type_business.string'  => 'The type business must be a string.',
            'type_business.max'     => 'The type business may not be greater than 255 characters.',

            'category_id.required' => 'The category field is required.',
            'category_id.integer'  => 'The category must be a valid id.',
            'category_id.exists'   => 'The selected category is invalid.',

            'country_id.required'  => 'The country field is required.',
            'country_id.integer'   => 'The country must be a valid id.',
            'country_id.exists'    => 'The selected country is invalid.',

            'city_id.required'     => 'The city field is required.',
            'city_id.integer'      => 'The city must be a valid id.',
            'city_id.exists'       => 'The selected city is invalid.',

            'id_card_front_image.required' => 'The front ID image is required.',
            'id_card_front_image.image'    => 'The front ID image must be an image.',
            'id_card_front_image.mimes'    => 'The front ID image must be jpeg, png, jpg, gif, or webp.',
            'id_card_front_image.max'      => 'The front ID image may not be greater than 4096 kilobytes.',

            'id_card_back_image.required'  => 'The back ID image is required.',
            'id_card_back_image.image'     => 'The back ID image must be an image.',
            'id_card_back_image.mimes'     => 'The back ID image must be jpeg, png, jpg, gif, or webp.',
            'id_card_back_image.max'       => 'The back ID image may not be greater than 4096 kilobytes.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_card_front_image' => 'front ID image',
            'id_card_back_image'  => 'back ID image',
        ];
    }
}
