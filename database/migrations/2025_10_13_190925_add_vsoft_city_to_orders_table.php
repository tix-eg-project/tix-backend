<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_vsoft_city_id')) {
                $table->unsignedBigInteger('shipping_vsoft_city_id')->nullable()->after('shipping_zone_id'); // [VSOFT]
            }
            if (!Schema::hasColumn('orders', 'shipping_vsoft_city_name')) {
                $table->string('shipping_vsoft_city_name')->nullable()->after('shipping_vsoft_city_id'); // [VSOFT]
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_vsoft_city_name')) {
                $table->dropColumn('shipping_vsoft_city_name'); // [VSOFT]
            }
            if (Schema::hasColumn('orders', 'shipping_vsoft_city_id')) {
                $table->dropColumn('shipping_vsoft_city_id'); // [VSOFT]
            }
        });
    }
};
