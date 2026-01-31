<?php

namespace App\Http\Controllers\Api\User\Privacy;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ReturnPolicy;

class ReturnPolicyController extends Controller
{
    public function show()
    {
        $policy = ReturnPolicy::first();

        if (!$policy) {
            return ApiResponseHelper::error('messages.not_found', 404);
        }

        return ApiResponseHelper::success('messages.retrieved_successfully', [
            'content' => $policy->content,
        ]);
    }
}
