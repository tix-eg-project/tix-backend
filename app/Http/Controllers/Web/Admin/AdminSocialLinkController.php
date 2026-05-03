<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\SocialLink\StoreSocialLinkRequest;
use App\Http\Requests\Web\Admin\SocialLink\UpdateSocialLinkRequest;
use App\Models\SocialLink;
use App\Services\Dashboard\SocialLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AdminSocialLinkController extends Controller
{
    protected SocialLinkService $socialLinkService;

    public function __construct(SocialLinkService $socialLinkService)
    {
        $this->socialLinkService = $socialLinkService;
    }

    public function index()
    {
        $links = $this->socialLinkService->index();
        return view('Admin.pages.social_links.index', compact('links'));
    }

    public function edit(SocialLink $link)
    {
        return view('Admin.pages.social_links.edit', compact('link'));
    }

    public function update(UpdateSocialLinkRequest $request, SocialLink $link): RedirectResponse
    {
        $this->socialLinkService->update($link, $request->validated());
        return Redirect::route('social-links.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(SocialLink $link): RedirectResponse
    {
        $this->socialLinkService->delete($link);
        return Redirect::route('social-links.index')->with('success', __('messages.deleted_successfully'));
    }
}
