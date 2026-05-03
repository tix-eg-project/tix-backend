<?php

namespace App\Http\Controllers\Web\Auth;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\Vendor\RegisterVendorRequest;
use App\Models\Vendor;
use App\Services\Dashboard\Vendor\VendorService;
use Illuminate\Http\Request;

class VendorRegisterController extends Controller
{
    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function register(RegisterVendorRequest $request)
    {
        $vendor = $this->vendorService->store($request->all());

        return ApiResponseHelper::success('messages.vendor_register_success', $vendor);
    }
}
