<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. أعمل جدول جديد مؤقت
        Schema::create('cart_items_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variant_item_id')
                ->nullable()
                ->constrained('product_variant_items')
                ->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_before', 10, 2);
            $table->decimal('unit_price_after', 10, 2);
            $table->decimal('unit_discount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['cart_id', 'product_id', 'product_variant_item_id'],
                'cart_item_unique'
            );
        });

        // 2. انقل البيانات من الجدول القديم للجدول الجديد
        DB::statement('INSERT INTO cart_items_temp SELECT * FROM cart_items');

        // 3. احذف الجدول القديم
        Schema::dropIfExists('cart_items');

        // 4. غير اسم الجدول الجديد للاسم الأصلي
        Schema::rename('cart_items_temp', 'cart_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse العملية
        Schema::create('cart_items_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_before', 10, 2);
            $table->decimal('unit_price_after', 10, 2);
            $table->decimal('unit_discount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id'], 'cart_item_unique');
        });

        DB::statement('INSERT INTO cart_items_old (id, cart_id, product_id, quantity, unit_price_before, unit_price_after, unit_discount, created_at, updated_at) 
                      SELECT id, cart_id, product_id, quantity, unit_price_before, unit_price_after, unit_discount, created_at, updated_at FROM cart_items');

        Schema::dropIfExists('cart_items');
        Schema::rename('cart_items_old', 'cart_items');
    }
};
