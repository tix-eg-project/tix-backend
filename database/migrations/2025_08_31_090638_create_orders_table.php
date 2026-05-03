<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cart_id')->nullable();


            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->unsignedBigInteger('shipping_zone_id')->nullable();
            $table->string('shipping_zone_name')->nullable();

            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('payment_method_name')->nullable();

            $table->string('coupon_code')->nullable();
            $table->string('coupon_type')->nullable();
            $table->decimal('coupon_value', 10, 2)->nullable();
            $table->decimal('coupon_amount', 10, 2)->default(0);

            $table->text('contact_address')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->text('order_note')->nullable();

            $table->string('status')->default('placed');
            $table->string('payment_status')->default('pending');

            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
