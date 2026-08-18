<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('grind_type')->default('whole_bean');
            $table->integer('weight_grams');
            $table->string('sku')->unique();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('product_id');
            $table->index('sku');
            $table->index('grind_type');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE product_variants ADD CONSTRAINT stock_non_negative CHECK (stock >= 0)');
            DB::statement('ALTER TABLE product_variants ADD CONSTRAINT price_positive CHECK (price > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
