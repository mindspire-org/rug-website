<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_dimension_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('label')->nullable(); // e.g. "6' x 9'"
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->string('shape')->nullable(); // rectangular, round, runner, etc.
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_dimension_prices');
    }
};
