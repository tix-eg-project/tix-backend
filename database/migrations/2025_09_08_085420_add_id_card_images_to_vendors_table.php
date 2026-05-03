<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'id_card_front_image')) {
                $table->string('id_card_front_image', 512)->nullable()->after('image');
            }
            if (!Schema::hasColumn('vendors', 'id_card_back_image')) {
                $table->string('id_card_back_image', 512)->nullable()->after('id_card_front_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'id_card_front_image')) {
                $table->dropColumn('id_card_front_image');
            }
            if (Schema::hasColumn('vendors', 'id_card_back_image')) {
                $table->dropColumn('id_card_back_image');
            }
        });
    }
};
