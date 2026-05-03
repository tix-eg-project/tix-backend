<?php

namespace App\Services;

use App\Models\ContactUs;
//use App\Services\Dashboard\AdminNotificationService;
use App\Notifications\DashboardNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

class ContactUsService
{
    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $contact = ContactUs::create($data);

            // AdminNotificationService::notifyAll(
            //     __('messages.new_contact_title'),
            //     __('messages.new_contact_message', ['name' => $data['full_name']]),
            //     route('admin.contact_us.index')

            // );

            $admins = Admin::all();
            foreach ($admins as $admin) {
                Log::info('Sending notification to admin ID: ' . $admin->id);
                $admin->notify(new DashboardNotification(__('messages.new_contact_message', ['name' => $data['full_name']]), route('contact_us.index')));
            }


            return $contact;
        });
    }

    public function index()
    {
        return ContactUs::latest()->get();
    }

    public function delete($contact)
    {
        return $contact->delete();
    }
}
