<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('vendor_id')->nullable();

            $table->string('product_name');
            $table->string('product_image')->nullable();

            $table->decimal('price_before', 10, 2);
            $table->decimal('price_after', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
