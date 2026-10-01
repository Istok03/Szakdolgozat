<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Older local installations already contain this manually created table.
        if (Schema::hasTable('category_options')) {
            if (!Schema::hasColumns('category_options', ['id', 'category_id', 'name', 'price', 'created_at', 'updated_at'])) {
                throw new RuntimeException('A category_options tábla szerkezete nem kompatibilis.');
            }

            return;
        }

        Schema::create('category_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Preserve the table on rollback: it may predate this migration and contain user data.
    }
};
