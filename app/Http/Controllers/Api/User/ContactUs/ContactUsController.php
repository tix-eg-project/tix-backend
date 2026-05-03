<?php

namespace App\Http\Controllers\Api\User\ContactUs;



use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Services\ContactUsService;
use App\Http\Requests\Api\ContactUs\ContactUsRequest;
use App\Helpers\ApiResponseHelper;

class ContactUsController extends Controller
{
    public function __construct(private ContactUsService $service) {}

    public function index()
    {
        $data = $this->service->index();
        return ApiResponseHelper::success(__('messages.retrieved_successfully'), $data);
    }

    public function store(ContactUsRequest $request)
    {
        $data = $this->service->store($request->validated());
        return ApiResponseHelper::success(__('messages.created_successfully'));
    }

    public function destroy(ContactUs $contact)
    {
        $this->service->delete($contact);
        return ApiResponseHelper::success(__('messages.deleted_successfully'));
    }
}
