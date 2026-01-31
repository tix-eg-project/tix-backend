<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stay_in_touch', function (Blueprint $table) {
            $table->id();
            $table->json('address');
            $table->json('phones');
            $table->json('work_hours');
            $table->json('email');
            $table->string('map_link');
            $table->string('whatsapp_link');
            $table->json('web_link');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stay_in_touch');
    }
};
