<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vsoft_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vsoft_city_id')->unique();
            $table->string('name');
            $table->unsignedInteger('vsoft_zone_id')->nullable(); // من VSoft
            $table->foreignId('shipping_zone_id')->nullable()->constrained('shipping_zones')->nullOnDelete(); // عندك
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vsoft_cities');
    }
};
