<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class VendorNotificationController extends Controller
{
    public function readAll(Request $request)
    {
        $user = Auth::guard('vendor')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return back()->with('success', __('All notifications marked as read.'));
    }

    public function open($id, Request $request)
    {
        $user = Auth::guard('vendor')->user();
        /** @var DatabaseNotification|null $notification */
        $notification = $user?->notifications()->where('id', $id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $target = $notification->data['vendor_url'] ?? null;

        // fallback آمن لصفحة أوامر الفندور
        if (!$target) {
            $target = route('vendor.orders.index');
        }

        return redirect()->to($target);
    }
}
