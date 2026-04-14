<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'features')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('features')->nullable();
            });
        }

        if (!Schema::hasTable('product_faqs')) {
            Schema::create('product_faqs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->json('question');
                $table->json('answer');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('review')->nullable();
                $table->timestamps();

                $table->unique(['product_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_reviews')) {
            Schema::dropIfExists('product_reviews');
        }
        if (Schema::hasTable('product_faqs')) {
            Schema::dropIfExists('product_faqs');
        }
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'features')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('features');
            });
        }
    }
};
