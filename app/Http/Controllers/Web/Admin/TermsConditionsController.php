<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ShippingPrivacy\ShippingPolicyRequest;
use App\Services\Dashboard\TermsConditionsService;
use Illuminate\Support\Facades\Redirect;

class TermsConditionsController extends Controller
{
    protected TermsConditionsService $service;

    public function __construct(TermsConditionsService $service)
    {
        $this->service = $service;
    }

    public function edit()
    {
        $policy = $this->service->createIfNotExist();
        return view('Admin.pages.terms_policy.edit', compact('policy'));
    }

    public function update(ShippingPolicyRequest $request)
    {
        $policy = $this->service->get();
        $this->service->update($policy, $request->validated());

        return Redirect::back()->with('success', __('messages.updated_successfully'));
    }
}
