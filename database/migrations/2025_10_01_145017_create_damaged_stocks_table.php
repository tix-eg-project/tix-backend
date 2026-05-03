<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('damaged_stocks', function (Blueprint $t) {
            $t->id();

            $t->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();

            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('product_variant_item_id')->nullable()->constrained()->nullOnDelete();

            $t->foreignId('return_request_id')->nullable()->constrained()->nullOnDelete();

            $t->unsignedInteger('quantity');

            // سبب التلف (كود + نص) — نفس منطق الأسباب للاسترجاع لو عايز توحّد
            $t->unsignedTinyInteger('reason_code')->nullable(); // ex: ReturnReasonEnum value
            $t->text('reason_text')->nullable();

            // (اختياري) مكان التخزين/سجل داخلي
            // $t->string('warehouse_location', 120)->nullable();

            $t->timestamps();

            // فهارس مفيدة
            $t->index(['vendor_id']);
            $t->index(['return_request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_stocks');
    }
};
