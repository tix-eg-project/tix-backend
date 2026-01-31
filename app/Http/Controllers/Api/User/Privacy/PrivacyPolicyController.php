<?php

namespace App\Http\Controllers\Api\User\Privacy;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function show()
    {
        $policy = PrivacyPolicy::first();

        if (!$policy) {
            return ApiResponseHelper::error('messages.not_found', 404);
        }

        return ApiResponseHelper::success('messages.retrieved_successfully', [
            'content' => $policy->content,
        ]);
    }
}
