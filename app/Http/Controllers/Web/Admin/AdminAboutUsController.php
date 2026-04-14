<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\AboutUs\StoreAboutUsRequest;
use App\Http\Requests\Web\Admin\AboutUs\UpdateAboutUsRequest;
use App\Models\AboutUs;
use App\Services\Dashboard\AboutUsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AdminAboutUsController extends Controller
{
    protected AboutUsService $aboutUsService;

    public function __construct(AboutUsService $aboutUsService)
    {
        $this->aboutUsService = $aboutUsService;
    }

    public function index()
    {
        $about = AboutUs::first();
        return view('Admin.pages.about.index', compact('about'));
    }

    public function edit()
    {
        $about = AboutUs::first();
        if (! $about) {
            return redirect()->route('about.create');
        }

        return view('Admin.pages.about.edit', compact('about'));
    }

    public function update(UpdateAboutUsRequest $request): RedirectResponse
    {
        $about = AboutUs::first();
        if (! $about) {
            return redirect()->route('about.create')->withErrors(__('messages.not_found'));
        }

        $this->aboutUsService->update($about, $request->validated());
        return Redirect::route('about.index')->with('success', __('messages.updated_successfully'));
    }

    public function create()
    {
        $about = AboutUs::first();
        if ($about) {
            return redirect()->route('about.edit');
        }

        return view('Admin.pages.about.create');
    }
public function store(StoreAboutUsRequest $request): RedirectResponse
    {
        $this->aboutUsService->store($request->validated());
        return Redirect::route('about.index')->with('success', __('messages.created_successfully'));
    }
    // public function store(StoreAboutUsRequest $request): RedirectResponse
    // {
    //     $this->aboutUsService->store($request->validated());
    //     return Redirect::route('about.index')->with('success', __('messages.created_successfully'));
 
   // }
}
