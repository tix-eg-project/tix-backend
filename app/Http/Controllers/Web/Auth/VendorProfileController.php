<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\Vendor\UpdateVendorProfileRequest;
use App\Services\Dashboard\Vendor\VendorService;
use Illuminate\Support\Facades\Hash;

class VendorProfileController extends Controller
{
    public function __construct(private VendorService $vendorService) {}

    public function edit()
    {
        $vendor = auth('vendor')->user();
        return view('Vendor.pages.profile.updateProfile', compact('vendor'));
    }

    public function update(UpdateVendorProfileRequest $request)
    {
        $vendor = auth('vendor')->user();
        $data   = $request->validated();

        // ✅ حدّث الباسورد هنا مباشرة (لو اتبعت) وبشكل مستقل عن الخدمة
        if (!empty($data['password'])) {
            $vendor->password = Hash::make($data['password']);
            $vendor->save();              // نحفظ الباسورد فورًا
            unset($data['password']);     // نشيله من الـ data قبل ما نبعته للخدمة
        }

        // باقي الحقول تمشي على الخدمة زي ما هي
        $this->vendorService->update($vendor, $data);

        return redirect()
            ->route('vendor.profile.edit')
            ->with('success', __('messages.updated_successfully'));
    }
}
