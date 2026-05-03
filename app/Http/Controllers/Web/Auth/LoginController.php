<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('Admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        // dd(Auth::user());
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        }
        // dd($credentials, Auth::guard('admin')->attempt($credentials), Auth::guard('admin')->user());

        return redirect()->route('login')
            ->withErrors(['Error' => 'Invalid login data'])
            ->withInput(input: $request->only('email'));
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
