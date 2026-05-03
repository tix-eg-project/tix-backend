<?php

// namespace App\Http\Requests\Api\Dashboard\Privacy;

// use App\Helpers\ValidationRuleHelper;
// use App\Traits\HttpResponse;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\ValidationException;

// class PrivacyPolicyRequest extends FormRequest
// {
//     use HttpResponse;

//     public function rules(): array
//     {
//         return [
//             'content' => 'array|between:2,2',
//             'content.ar' => ValidationRuleHelper::longTextRules(['required']),
//             'content.en' => ValidationRuleHelper::longTextRules(['required']),
//         ];
//     }

//     /**
//      * @throws ValidationException
//      */
//     public function failedValidation(Validator $validator): void
//     {
//         $this->throwValidationException($validator);
//     }
// }
