<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyCodeMail;
use App\Helpers\ApiResponseHelper;

class UserAuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'email'    => $data['email'],
            'name'     => $data['name'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,

            'password' => bcrypt($data['password']),
        ]);

        $code = rand(100000, 999999);
        Cache::put('verify_code_' . $user->email, $code, now()->addMinutes(10));

        Mail::to($user->email)->send(new VerifyCodeMail($code));

        return ApiResponseHelper::success(
            'messages.register_success',
            ['message' => 'Verification code sent to your email.']
        );
    }

    public function verifyCode(string $email, int $code)
    {
        $cached = Cache::get('verify_code_' . $email);

        if (! $cached || $cached != $code) {
            return ApiResponseHelper::error(
                'messages.verify_code_failed',
                422
            );
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.user_not_found',
                404
            );
        }

        $user->email_verified_at = now();
        $user->save();
        Cache::forget('verify_code_' . $email);

        return ApiResponseHelper::success(
            'messages.verify_success'
        );
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return ApiResponseHelper::error(
                'messages.invalid_credentials',
                401
            );
        }

        if (is_null($user->email_verified_at)) {
            return ApiResponseHelper::error(
                'messages.email_not_verified',
                403
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponseHelper::success(
            'messages.login_success',
            ['token' => $token]
        );
    }

    public function sendResetCode(string $email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.email_not_registered',
                404
            );
        }

        if (! is_null($user->email_verified_at)) {
            return ApiResponseHelper::success(
                'messages.account_already_verified'
            );
        }

        $code = rand(100000, 999999);
        Cache::put('verify_code_' . $email, $code, now()->addMinutes(10));

        Mail::to($email)->send(new VerifyCodeMail($code));

        return ApiResponseHelper::success(
            'messages.reset_code_sent',
            ['message' => 'Verification code sent to your email.']
        );
    }

    public function forgetPassword(string $email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.email_not_registered',
                404
            );
        }

        $code = rand(100000, 999999);
        Cache::put('reset_code_' . $email, $code, now()->addMinutes(10));

        Mail::to($email)->send(new VerifyCodeMail($code));

        return ApiResponseHelper::success(
            'messages.forget_password_code_sent',
            ['message' => 'Verification code sent to your email.']
        );
    }
    public function resendResetCode(string $email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.email_not_registered',
                404
            );
        }

        $code = rand(100000, 999999);
        Cache::put('verify_code_' . $email, $code, now()->addMinutes(10));

        Mail::to($email)->send(new VerifyCodeMail($code));

        return ApiResponseHelper::success(
            'messages.resend reset_password_code_sent',
            ['message' => 'Verification code sent to your email.']
        );
    }

    public function verifyResetCode(string $email, int $code)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.user_not_found',
                404
            );
        }

        $cacheKey = 'reset_code_' . $email;
        $cached   = Cache::get($cacheKey);

        if (! $cached) {
            return ApiResponseHelper::error(
                'messages.code_expired',
                422
            );
        }

        if ($cached != $code) {
            return ApiResponseHelper::error(
                'messages.code_mismatch',
                422
            );
        }

        return ApiResponseHelper::success(
            'messages.reset_verify_success'
        );
    }

    public function resetPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return ApiResponseHelper::error(
                'messages.user_not_found',
                404
            );
        }

        $cacheKey = 'reset_code_' . $data['email'];
        if (! Cache::has($cacheKey)) {
            return ApiResponseHelper::error(
                'messages.must_verify_code_first',
                422
            );
        }

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $user->password = bcrypt($data['New_password']);
        $user->save();

        Cache::forget($cacheKey);

        return ApiResponseHelper::success(
            'messages.password_reset_success'
        );
    }
}
