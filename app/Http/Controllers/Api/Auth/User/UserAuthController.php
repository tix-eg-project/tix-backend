<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\User\LoginRequest;
use App\Http\Requests\Api\Auth\User\RegisterRequest;
use App\Http\Requests\Api\Auth\User\SendResetCodeRequest;
use App\Http\Requests\Api\Auth\User\VerifyCodeRequest;
use App\Http\Requests\Api\Auth\User\VerifyResetCodeRequest;
use App\Http\Requests\Api\Auth\User\ResetPasswordRequest;
use App\Services\Auth\UserAuthService;
use App\Services\UserContactService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\User\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;


class UserAuthController extends Controller
{
    protected UserAuthService $authService;


    public function __construct(UserAuthService $authService,)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    public function verify(VerifyCodeRequest $request)
    {
        return $this->authService->verifyCode($request->email, $request->code);
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function sendResetCode(SendResetCodeRequest $request)
    {
        return $this->authService->sendResetCode($request->email);
    }

    public function forgetPassword(SendResetCodeRequest $request)
    {
        return $this->authService->forgetPassword($request->email);
    }

    public function resendResetCode(SendResetCodeRequest $request)
    {
        return $this->authService->resendResetCode($request->email);
    }

    public function verifyResetCode(VerifyResetCodeRequest $request)
    {
        return $this->authService->verifyResetCode($request->email, $request->code);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->authService->resetPassword($request->validated());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponseHelper::success(

            __('messages.logout_success')
        );
    }
}
