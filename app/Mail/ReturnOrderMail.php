<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Mail\Mailable;

class ReturnOrderMail extends Mailable
{
    public ReturnRequest $req;
    public string $variant;

    public function __construct(ReturnRequest $req, string $variant = 'approved_pickup')
    {
        $this->req     = $req->loadMissing(['order','orderItem','user']);
        $this->variant = $variant; // هنستخدمه فقط للوضوح، بس الرسالة هنا مخصصة للـ approved_pickup
    }

    public function build()
    {
        $orderNumber = $this->req->order->order_number ?? ('#'.$this->req->order_id);
        $userName    = $this->req->user->name ?? 'عميلنا العزيز';
        $qty         = (int) $this->req->quantity;
        $productName = $this->req->orderItem->name
                    ?? ($this->req->orderItem->product->name ?? 'المنتج');
        $pickupDate  = $this->req->approved_at
                    ? $this->req->approved_at->copy()->addDays(2)->format('Y-m-d')
                    : null;

        $subject = "تم قبول طلب الاسترجاع — الطلب {$orderNumber}";

        // HTML مضمَّن بالكامل (من غير Blade)
        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>{$subject}</title>
</head>
<body style="font-family:Tahoma,Arial,sans-serif;background:#f7f7f7;margin:0;padding:20px;">
  <table width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;">
    <tr><td style="padding:24px;">
      <h2 style="margin:0 0 12px;">مرحبًا {$userName}،</h2>
      <p style="margin:0 0 8px;">تم قبول طلب الاسترجاع الخاص بك للطلب {$orderNumber}.</p>
      <p style="margin:0 0 8px;">
        سيتم التنسيق للتواصل أو استلام المنتج من عنوانك
        <!-- خلال يومين عمل أو حتى تاريخ محدد -->
        <!-- لو عندنا تاريخ -->
        <!-- نعرضه -->
      </p>
HTML;

        if ($pickupDate) {
            $html .= "<p style=\"margin:0 0 8px;\">بحد أقصى حتى {$pickupDate}.</p>";
        } else {
            $html .= "<p style=\"margin:0 0 8px;\">خلال يومي عمل.</p>";
        }

        $html .= <<<HTML
      <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">

      <h4 style="margin:0 0 8px;">ملخص المرتجع</h4>
      <ul style="margin:0;padding:0 18px;">
        <li>رقم المرتجع: #{$this->req->id}</li>
        <li>المنتج: {$productName}</li>
        <li>الكمية: {$qty}</li>
      </ul>

      <p style="margin:16px 0 0;color:#666;font-size:13px;">
        لأي استفسار، يُرجى الرد على هذه الرسالة أو التواصل مع دعم العملاء.
      </p>
    </td></tr>
  </table>
</body>
</html>
HTML;

        return $this->subject($subject)
                    ->html($html);
    }
}
