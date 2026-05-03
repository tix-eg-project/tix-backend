<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vsoft_shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // بيانات الشحنة الأساسية
            $table->unsignedBigInteger('vsoft_city_id')->nullable()->index();
            $table->unsignedInteger('product_id')->nullable(); // 8=Domestic, 5=COD ...
            $table->decimal('cod', 12, 2)->default(0);
            $table->decimal('weight', 8, 3)->default(1);
            $table->unsignedInteger('pieces')->default(1);

            // سناب شوت للسعر/الزون وقت الإنشاء (اختياري)
            $table->foreignId('shipping_zone_id')->nullable()->constrained('shipping_zones')->nullOnDelete();
            $table->decimal('price_snapshot', 12, 2)->nullable();

            // البوليصة وحالة الإرسال
            $table->string('awb')->nullable()->index();
            $table->enum('status', ['pending', 'pushed', 'failed'])->default('pending');
            $table->timestamp('pushed_at')->nullable();
            $table->unsignedInteger('retries')->default(0);
            $table->text('last_error')->nullable();

            // حفظ الـpayloads للتدقيق
            $table->json('payload_request')->nullable();
            $table->json('payload_response')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vsoft_shipments');
    }
};
