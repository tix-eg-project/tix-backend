<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_vsoft_city_id')->nullable()->after('shipping_zone_id');
            $table->string('shipping_vsoft_city_name')->nullable()->after('shipping_vsoft_city_id');
            $table->string('vsoft_awb')->nullable()->after('id');
            $table->json('vsoft_payload')->nullable()->after('vsoft_awb');
        });
    }
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_vsoft_city_id', 'shipping_vsoft_city_name', 'vsoft_awb', 'vsoft_payload']);
        });
    }
};
