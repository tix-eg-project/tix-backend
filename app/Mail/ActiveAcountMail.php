<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActiveAcountMail extends Mailable
{
    use Queueable, SerializesModels;

    public bool $approved;
    public ?string $loginUrl;
    public string $vendorName;

    /**
     * @param bool        $approved   هل تمّت الموافقة أم لا
     * @param string      $vendorName اسم/شركة الفندور لعرضه في الإيميل
     * @param string|null $loginUrl   رابط تسجيل الدخول (لو Approved فقط)
     */
    public function __construct(bool $approved, string $vendorName, ?string $loginUrl = null)
    {
        $this->approved   = $approved;
        $this->vendorName = $vendorName;
        $this->loginUrl   = $loginUrl;
    }

    public function build()
    {
        $subject = $this->approved
            ? 'تمت الموافقة على حسابك كتاجر'
            : 'تم رفض/إيقاف حساب التاجر';

        // مهم: القالب ده موجود باسم access_account.blade.php
        // وتحت مجلد resources/views/emails/
        return $this->subject($subject)
            ->markdown('emails.access_account', [
                'approved'   => $this->approved,
                'vendorName' => $this->vendorName,
                'loginUrl'   => $this->loginUrl,
            ]);
    }
}
