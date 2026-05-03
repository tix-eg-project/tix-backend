<?php

namespace App\Models;

use App\Enums\RefundMethodEnum;
use App\Enums\ReturnReasonEnum;
use App\Enums\ReturnStatusEnum;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\OrderItem;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'vendor_id',
        'user_id',
        'quantity',
        'status',
        'reason_code',
        'reason_text',
        'return_address',
        'payout_wallet_phone',
        'refund_method',
        'refund_subtotal',
        'refund_fee',
        'refund_shipping',
        'refund_total',
        'approved_at',
        'received_at',
        'refunded_at',
    ];

    protected $casts = [
        'status'         => ReturnStatusEnum::class,
        'refund_method'  => RefundMethodEnum::class,
        'reason_code'    => ReturnReasonEnum::class,

        'return_address' => 'array',
        'approved_at'    => 'datetime',
        'received_at'    => 'datetime',
        'refunded_at'    => 'datetime',
    ];

    /* ===== العلاقات ===== */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // هنضيف الموديل ده لاحقًا في جدول credit_notes
    public function creditNote()
    {
        return $this->hasOne(CreditNote::class);
    }

    /* ===== سكوبات مفيدة ===== */
    public function scopeForVendor($q, $vendorId)
    {
        return $q->where('vendor_id', $vendorId);
    }

    public function scopeStatus($q, ReturnStatusEnum $status)
    {
        return $q->where('status', $status->value);
    }
    public function getStatusLabelAttribute(): string
    {
        $status = $this->status;

        if ($status instanceof ReturnStatusEnum) {
            return $status->label();
        }

        return ReturnStatusEnum::tryFrom((int)$status)?->label() ?? (string)$status;
    }

    public function getReasonLabelAttribute(): ?string
    {
        $reason = $this->reason_code;
        if ($reason instanceof ReturnReasonEnum) return $reason->label();
        if (is_numeric($reason)) return ReturnReasonEnum::tryFrom((int)$reason)?->label();
        return null;
    }

    public function getRefundMethodLabelAttribute(): ?string
    {
        $m = $this->refund_method;
        if ($m instanceof RefundMethodEnum) return $m->label();
        if (is_numeric($m)) return RefundMethodEnum::tryFrom((int)$m)?->label();
        return null;
    }
}
