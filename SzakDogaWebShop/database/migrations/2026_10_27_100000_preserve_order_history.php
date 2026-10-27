<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // SQLite must disable constraints outside a transaction before rebuilding a parent table.
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('product_name')->nullable();
            });
            DB::table('order_items')->orderBy('id')->chunkById(500, function ($items) {
                $names = DB::table('products')->whereIn('id', $items->pluck('product_id'))->pluck('name', 'id');
                foreach ($items as $item) {
                    DB::table('order_items')->where('id', $item->id)->update([
                        'product_name' => $names[$item->product_id] ?? 'Törölt termék',
                    ]);
                }
            });
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->unsignedBigInteger('user_id')->nullable()->change();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->unsignedBigInteger('product_id')->nullable()->change();
                $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            });
        });
    }

    public function down(): void
    {
        // Never discard orders whose account or product has been deleted.
        if (DB::table('orders')->whereNull('user_id')->exists()
            || DB::table('order_items')->whereNull('product_id')->exists()) {
            throw new RuntimeException('A visszaállítás törölt fiókhoz vagy termékhez tartozó rendelést veszélyeztetne.');
        }
        Schema::withoutForeignKeyConstraints(function () {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->unsignedBigInteger('product_id')->nullable(false)->change();
                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
                $table->dropColumn('product_name');
            });
        });
    }
};
