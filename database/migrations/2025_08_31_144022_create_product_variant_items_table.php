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
        Schema::create('product_variant_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->json('selections');

            $table->string('options_key');

            $table->decimal('price', 10, 2);

            // $table->string('sku')->nullable();
            // $table->string('barcode')->nullable();
            $table->integer('quantity')->default(0);
            // $table->string('image')->nullable();
            // $table->tinyInteger('is_active')->default(1);

            $table->timestamps();

            $table->unique(['product_id', 'options_key']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_items');
    }
};
