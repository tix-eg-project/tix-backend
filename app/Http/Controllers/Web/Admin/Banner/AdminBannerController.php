<?php

namespace App\Http\Controllers\Web\Admin\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Web\Admin\Banner\UpdateBannerRequest;
use App\Models\Banner;
use App\Models\Vendor;
use App\Services\Dashboard\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AdminBannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        $search    = request('search');
        $vendorId  = request('vendor_id');
        $query = Banner::query()
            ->with('vendor')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title->ar', 'like', '%' . $search . '%')
                    ->orWhere('title->en', 'like', '%' . $search . '%');
            });
        }

        if (!empty($vendorId)) {
            $query->where('vendor_id', $vendorId);
        }

        $banners = $query->paginate(10);

        $vendors = Vendor::select('id', 'name')->orderBy('name')->get();

        return View::make('Admin.pages.banners.index', compact('banners', 'vendors', 'vendorId', 'search'));
    }

    public function create()
    {
        $vendors = Vendor::select('id', 'name')->orderBy('name')->get();
        return View::make('Admin.pages.banners.create', compact('vendors'));
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $this->bannerService->store($request->validated());
        return Redirect::route('banners.index')->with('success', __('messages.banner_created'));
    }

    public function edit(Banner $banner)
    {
        $vendors = Vendor::select('id', 'name')->orderBy('name')->get();
        return View::make('Admin.pages.banners.edit', compact('banner', 'vendors'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->bannerService->update($banner, $request->validated());
        return Redirect::route('banners.index')->with('success', __('messages.banner_updated'));
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->bannerService->delete($banner);
        return Redirect::route('banners.index')->with('success', __('messages.banner_deleted'));
    }
}
