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
        Schema::table('comments', function (Blueprint $table) {
            // حذف جميع التعليقات القديمة على المنتجات
            \DB::table('comments')->truncate();
            
            // إزالة العلاقة مع المنتجات
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            
            // إضافة العلاقة مع الطلبات
            $table->foreignId('order_id')->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // حذف العلاقة مع الطلبات
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            
            // إعادة العلاقة مع المنتجات
            $table->foreignId('product_id')->after('user_id')->constrained()->cascadeOnDelete();
        });
    }
};
