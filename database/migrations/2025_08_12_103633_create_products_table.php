<?php

use App\Enums\AmountType;
use App\Enums\Status;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('short_description')->nullable();
            $table->json('long_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->decimal('discount')->nullable();
            $table->tinyInteger('discount_type')->default(AmountType::fixed);


            $table->json('images')->nullable();

            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
            // $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete();

            $table->tinyInteger('status')->default(Status::Active);
            $table->timestamps();

            $table->index(['category_id', 'brand_id', 'vendor_id', 'subcategory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
