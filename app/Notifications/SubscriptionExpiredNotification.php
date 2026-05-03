<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification
{

    use Queueable;

    protected $title;
    protected $body;
    protected $url;

    public function __construct($title, $body, $url)
    {
        $this->title = $title;
        $this->body  = $body;
        $this->url   = $url;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => $this->url,
            'date'  => now()->diffForHumans(),
        ];
    }
}

