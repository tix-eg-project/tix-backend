<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ShippingPrivacy\ShippingPolicyRequest;
use App\Services\Dashboard\ReturnPolicyService;
use Illuminate\Support\Facades\Redirect;

class ReturnPolicyController extends Controller
{
    protected ReturnPolicyService $service;

    public function __construct(ReturnPolicyService $service)
    {
        $this->service = $service;
    }

    public function edit()
    {
        $policy = $this->service->createIfNotExist();
        return view('Admin.pages.return_policy.edit', compact('policy'));
    }

    public function update(ShippingPolicyRequest $request)

    {
        $policy = $this->service->get();
        $this->service->update($policy, $request->validated());

        return Redirect::back()->with('success', __('messages.updated_successfully'));
    }
}
