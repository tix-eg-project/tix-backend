<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class LoginVendorController extends Controller
{
    public function showLoginForm()
    {
        return view('Vendor.auth.login');
    }

    public function login(LoginRequest $request)
    {

        $credentials = $request->only('email', 'password');


        if (Auth::guard('vendor')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $vendor = Auth::guard('vendor')->user();


            if ($vendor->status != 1) {
                Auth::guard('vendor')->logout();
                return redirect()->route('vendor.login')
                    ->withErrors([__('messages.account_not_active')])
                    ->withInput($request->only('email'));
            }



            return redirect()->route(route: 'vendor.dashboard')->with('success', __('messages.login_successful'));
        }


        return redirect()->route('vendor.login')
            ->withErrors([__('messages.invalid_login_data')])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login');
    }
}
