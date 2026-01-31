<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_variant_item_id')) {
                $table->unsignedBigInteger('product_variant_item_id')
                    ->nullable()
                    ->after('product_id');

                // إضافة foreign key constraint
                $table->foreign('product_variant_item_id')
                    ->references('id')
                    ->on('product_variant_items')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_item_id']);

            if (Schema::hasColumn('order_items', 'product_variant_item_id')) {
                $table->dropColumn('product_variant_item_id');
            }
        });
    }
};
