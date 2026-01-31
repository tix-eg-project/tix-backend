<?php

namespace App\Http\Controllers\Api\Auth\Vendor;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\Vendor\RegisterVendorRequest;
use App\Services\Dashboard\Vendor\VendorService;

class RegisterVendorController extends Controller
{
    protected $vendorService;
    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }
    public function register(RegisterVendorRequest $request)
    {
        $vendor = $this->vendorService->store($request->all());
        return ApiResponseHelper::success('messages.Registration completed. Awaiting admin review. You will receive a message on your registered email address.');
    }
}
