<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method')->nullable();
            $table->string('shipping_label')->nullable();
            $table->string('pickup_address')->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->boolean('manages_stock')->default(false);
            $table->boolean('stock_reserved')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'shipping_label', 'pickup_address', 'shipping_fee', 'manages_stock', 'stock_reserved']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
