<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\StayInTouch\StayInTouchRequest;
use App\Services\Dashboard\StayInTouchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class AdminStayInTouchController extends Controller
{
    protected StayInTouchService $stayInTouchService;

    public function __construct(StayInTouchService $stayInTouchService)
    {
        $this->stayInTouchService = $stayInTouchService;
    }

    /**
     * Display the current Stay In Touch data.
     */
    public function index()
    {
        $data = $this->stayInTouchService->get();
        return view('Admin.pages.stay_in_touch.index', compact('data'));
    }

    /**
     * Edit the existing Stay In Touch data.
     */
    public function edit()
    {
        $data = $this->stayInTouchService->get();
        return view('Admin.pages.stay_in_touch.edit', compact('data'));
    }

    /**
     * Update or create the Stay In Touch data.
     */
    public function update(StayInTouchRequest $request): RedirectResponse
    {
        $this->stayInTouchService->storeOrUpdate($request->validated());

        return Redirect::route('stay-in-touch.index')
            ->with('success', __('messages.updated_successfully'));
    }
}
