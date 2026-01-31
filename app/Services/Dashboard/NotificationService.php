<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function index($limit = 10)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate($limit);
        $lang = request()->header('Accept-Language', 'ar');

        $notes = $notifications->map(function ($note) use ($lang) {
            $data = $note->data;

            $titleKey = 'title_' . $lang;
            $bodyKey = 'body_' . $lang;

            return [
                'id'    => $note->id,
                'title' => $data[$titleKey] ?? '',
                'body'  => $data[$bodyKey] ?? '',
                'date'  => Carbon::parse($note->created_at)->diffForHumans(),
            ];
        });

        return [
            'notifications' => $notes,
        ];
    }
}
