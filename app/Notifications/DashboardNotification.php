<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\SendNotificationHelper;
use Illuminate\Notifications\Messages\MailMessage;


class DashboardNotification extends Notification
{
    // لا تستخدم ShouldQueue و Queueable مؤقتًا
    protected $message;
    protected $url;

    public function __construct($message, $url = null)
    {
        $this->message = $message;
        $this->url = $url;  // رابط لوحة تحكم التاجر
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,  // يمكن عرض الرابط مع الإشعار في الواجهة
        ];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('إشعار جديد من النظام')
            ->greeting('مرحبًا ' . $notifiable->name)
            ->line($this->message);

        if ($this->url) {
            $mail->action('اذهب إلى لوحة التحكم', $this->url);
        }

        return $mail->line('شكرًا لاستخدامك منصتنا!');
    }
}
