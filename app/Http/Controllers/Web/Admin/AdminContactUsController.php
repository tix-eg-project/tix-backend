<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Services\ContactUsService;
use Illuminate\Http\Request;

class AdminContactUsController extends Controller
{
    protected $contactUsService;

    public function __construct(ContactUsService $contactUsService)
    {
        $this->contactUsService = $contactUsService;
    }

    public function index(Request $request)
    {
        $query = ContactUs::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('subject', 'like', "%$search%")
                    ->orWhere('message', 'like', "%$search%");
            });
        }

        $messages = $query->latest()->paginate(10);

        return view('Admin.pages.contact_us.index', compact('messages'));
    }



    public function destroy(ContactUs $contactUs)
    {
        $this->contactUsService->delete($contactUs);
        return redirect()->back()->with('success', __('messages.deleted_successfully'));
    }
}
