<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'product_variant_item_id')) {
                $table->unsignedBigInteger('product_variant_item_id')
                    ->nullable()
                    ->after('product_id');
            }
        });

        if (Schema::hasTable('product_variant_items')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreign('product_variant_item_id', 'cart_items_variant_item_fk')
                    ->references('id')->on('product_variant_items')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        try {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropUnique('cart_item_unique');
            });
        } catch (\Throwable $e) {
        }

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_id', 'product_variant_item_id'], 'cart_items_cart_prod_var_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_prod_var_idx');
        });

        try {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropForeign('cart_items_variant_item_fk');
            });
        } catch (\Throwable $e) {
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'product_variant_item_id')) {
                $table->dropColumn('product_variant_item_id');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id'], 'cart_item_unique');
        });
    }
};
