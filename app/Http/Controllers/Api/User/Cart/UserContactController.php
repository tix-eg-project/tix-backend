<?php

namespace App\Http\Controllers\Api\User\Cart;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\StoreUserContactRequest;
use App\Services\Cart\UserContactService;

class UserContactController extends Controller
{
    public function __construct(protected UserContactService $service) {}

    public function show()
    {
        $data = $this->service->show();
        return ApiResponseHelper::success('messages.user_contact.retrieved', $data);
    }

    public function store(StoreUserContactRequest $request)
    {
        $data = $this->service->upsert($request->validated());
        return ApiResponseHelper::success('messages.user_contact.saved', $data);
    }

    public function destroy()
    {
        $this->service->delete();
        return ApiResponseHelper::success('messages.user_contact.deleted');
    }
}
