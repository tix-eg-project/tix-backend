<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Auth\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $service) {}

    public function show()
    {
        try {
            $data = $this->service->me();
            return ApiResponseHelper::success(__('messages.profile.show'), $data);
        } catch (\Throwable $e) {
            report($e);
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->user()?->id),
                ],
                'phone'   => 'required|string|max:30',
                'address' => 'required|string|max:500',
                'image'   => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:102400',


                'password'          => 'nullable|string|min:8|confirmed',
                'current_password'  => 'required_with:password|string',
            ]);

            $data = $this->service->update($validated);

            return ApiResponseHelper::success(__('messages.profile.updated'), $data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponseHelper::error(__('messages.validation_error'), 422, $e->errors());
        } catch (\Throwable $e) {
            report($e);
            return ApiResponseHelper::error(__('messages.something_wrong'), 500);
        }
    }
}
