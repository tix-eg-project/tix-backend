<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $t) {
            $t->id();

            // روابط الطلب والسطر والبائع والمستخدم
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            // الكمية المطلوبة في الاسترجاع (لازم <= كمية السطر)
            $t->unsignedInteger('quantity');

            // الحالة (int) حسب ReturnStatusEnum
            $t->unsignedTinyInteger('status')->default(1); // PendingReview

            // سبب الاسترجاع: كود (enum int) + نص حر اختياري
            $t->unsignedTinyInteger('reason_code')->nullable(); // ReturnReasonEnum
            $t->text('reason_text')->nullable();

            // عنوان بديل للاستلام + هاتف محفظة (مثلاً فودافون كاش)
            $t->json('return_address')->nullable(); // {name, phone, city, address1, address2, notes}
            $t->string('payout_wallet_phone', 30)->nullable();

            // طريقة رد المبلغ (RefundMethodEnum): 1 original, 2 wallet, 3 cash
            $t->unsignedTinyInteger('refund_method')->nullable();

            // مبالغ معتمدة (بعد توزيع الكوبون والاستقطاع)
            $t->decimal('refund_subtotal', 10, 2)->default(value: 0);  // صافي المنتجات
            $t->decimal('refund_fee', 10, 2)->default(0);       // استقطاع/رسوم
            $t->decimal('refund_shipping', 10, 2)->default(0);  // رد/خصم شحن
            $t->decimal('refund_total', 10, 2)->default(0);     // الإجمالي النهائي

            // طوابع زمنية للمراحل
            $t->timestamp('approved_at')->nullable(); // تم قبول الطلب مبدئياً (قيد الاسترجاع)
            $t->timestamp('received_at')->nullable(); // تم استلام المرتجع فعلياً
            $t->timestamp('refunded_at')->nullable(); // تم ردّ المبلغ

            $t->softDeletes();
            $t->timestamps();

            // فهارس تسريع
            $t->index(['vendor_id', 'status']);
            $t->index('order_id');
            $t->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
