<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create product_dimension_prices if missing
        if (!Schema::hasTable('product_dimension_prices')) {
            Schema::create('product_dimension_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('label')->nullable();
                $table->decimal('width', 10, 2)->nullable();
                $table->decimal('length', 10, 2)->nullable();
                $table->string('shape')->default('rectangular');
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('sale_price', 12, 2)->nullable();
                $table->integer('stock')->default(0);
                $table->boolean('is_default')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Create product_filter_attributes if missing
        if (!Schema::hasTable('product_filter_attributes')) {
            Schema::create('product_filter_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('display_name');
                $table->string('type')->default('multiselect');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Create product_filter_values if missing
        if (!Schema::hasTable('product_filter_values')) {
            Schema::create('product_filter_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_filter_attribute_id')->constrained('product_filter_attributes')->onDelete('cascade');
                $table->string('value');
                $table->string('display_value')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Create product_filter_value pivot if missing
        if (!Schema::hasTable('product_filter_value')) {
            Schema::create('product_filter_value', function (Blueprint $table) {
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_filter_value_id')->constrained('product_filter_values')->onDelete('cascade');
                $table->foreignId('product_filter_attribute_id')->constrained('product_filter_attributes')->onDelete('cascade');
                $table->primary(['product_id', 'product_filter_value_id']);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_filter_value');
        Schema::dropIfExists('product_filter_values');
        Schema::dropIfExists('product_filter_attributes');
        Schema::dropIfExists('product_dimension_prices');
    }
};
