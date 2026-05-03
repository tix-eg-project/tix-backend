<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'number',
        'return_request_id',
        'order_id',
        'user_id',
        'vendor_id',
        'pdf_path',
        'issued_by_admin_id',
    ];

    /* ===== العلاقات ===== */
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /* ===== هيلبرز اختيارية ===== */

    // رابط تحميل الـPDF لو مخزّن
    public function pdfUrl(): ?string
    {
        return $this->pdf_path ? route('admin.credit_notes.download', $this->id) : null;
    }
    public function items()
    {
        return $this->hasMany(\App\Models\CreditNoteItem::class);
    }
}
