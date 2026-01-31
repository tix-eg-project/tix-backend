<?php

namespace App\Services\Dashboard;

use App\Models\Vendor;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActiveAcountMail;

class VendoreService
{
    public function index()
    {
        $search = request('search');
        $query = Vendor::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function destroy($id)
    {
        Vendor::query()->findOrFail($id)->delete();
    }

    public function show($id)
    {
        return Vendor::query()->findOrFail($id);
    }

    public function updateStatus($status, Vendor $vendor)
    {
        $vendor->update(['status' => $status]);

        // إرسال البريد للفندور في الحالتين (قبول/رفض)
        $approved   = ((int) $status === 1);
        $loginUrl   = 'https://admin.tix-eg.com/vendor/login';
        $vendorName = $vendor->company_name ?? $vendor->name ?? 'عزيزنا المستخدم';

        Mail::to($vendor->email)->send(
            new ActiveAcountMail(
                approved: $approved,
                vendorName: $vendorName,
                loginUrl: $approved ? $loginUrl : null
            )
        );

        return true;
    }
}
