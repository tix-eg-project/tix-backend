<?php

// namespace App\Http\Controllers\Api\Privacy;

// use App\Helpers\ApiResponseHelper;
// use App\Http\Controllers\Controller;
// use App\Models\ShippingPolicy;

// class DeliveryPolicyController extends Controller
// {
//     public function show()
//     {
//         $policy = ShippingPolicy::first();

//         if (!$policy) {
//             return ApiResponseHelper::error('messages.not_found', 404);
//         }

//         return ApiResponseHelper::success('messages.retrieved_successfully', [
//             'content' => $policy->content,
//         ]);
//     }
// }
