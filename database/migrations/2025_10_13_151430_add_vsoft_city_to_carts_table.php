<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'shipping_vsoft_city_id')) {
                $table->unsignedBigInteger('shipping_vsoft_city_id')->nullable()->after('shipping_zone_id');
            }
            if (!Schema::hasColumn('carts', 'shipping_vsoft_city_name')) {
                $table->string('shipping_vsoft_city_name')->nullable()->after('shipping_vsoft_city_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'shipping_vsoft_city_name')) {
                $table->dropColumn('shipping_vsoft_city_name');
            }
            if (Schema::hasColumn('carts', 'shipping_vsoft_city_id')) {
                $table->dropColumn('shipping_vsoft_city_id');
            }
        });
    }
};
