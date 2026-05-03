<?php

namespace App\Http\Controllers\Api\User\Social;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Helpers\ApiResponseHelper;

class UserSocialLinkController extends Controller
{
    public function index()
    {
        $links = SocialLink::select('platform', 'url')->get();
        return ApiResponseHelper::success('messages.social_links.list', $links);
    }
}
